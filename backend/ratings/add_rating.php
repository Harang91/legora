<?php

require_once __DIR__ . '/../shared/init.php';

// Csak POST kérés engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak POST engedélyezett)");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$rater_id = $_SESSION['user_id'];

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);

$rated_user_id = $input['rated_user_id'] ?? null;
$rating        = isset($input['rating']) ? (int)$input['rating'] : null;
$comment       = $input['comment'] ?? null;

// Validáció
if (!$rated_user_id || !$rating || $rating < 1 || $rating > 5) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó mezők (rated_user_id, rating 1-5 között kötelező)");
}

// Saját magát nem értékelheti
if ($rater_id === (int)$rated_user_id) {
    http_response_code(403);
    errorResponse("Saját magadat nem értékelheted");
}

try {
    // Completed rendelés ellenőrzése
    $checkOrder = $pdo->prepare("
        SELECT COUNT(*)
        FROM orders
        WHERE buyer_id = ?
          AND seller_id = ?
          AND status = 'completed'
    ");
    $checkOrder->execute([$rater_id, $rated_user_id]);
    $hasOrder = $checkOrder->fetchColumn();

    if (!$hasOrder) {
        http_response_code(403);
        errorResponse("Csak akkor értékelhetsz, ha már vásároltál ettől az eladótól");
    }

    // Meglévő értékelés ellenőrzése
    $checkRating = $pdo->prepare("SELECT id FROM ratings WHERE rater_id = ? AND rated_user_id = ?");
    $checkRating->execute([$rater_id, $rated_user_id]);
    $existing = $checkRating->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Frissítés
        $upd = $pdo->prepare("UPDATE ratings SET rating = ?, comment = ?, rated_at = NOW() WHERE id = ?");
        $upd->execute([$rating, $comment, $existing['id']]);

        successResponse("Értékelés frissítve", [
            "rating_id" => (int)$existing['id'],
            "rating" => $rating,
            "comment" => $comment
        ]);
    } else {
        // Új értékelés
        $ins = $pdo->prepare("
            INSERT INTO ratings (rater_id, rated_user_id, rating, comment, rated_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $ins->execute([$rater_id, $rated_user_id, $rating, $comment]);

        successResponse("Értékelés sikeresen hozzáadva", [
            "rating_id" => (int)$pdo->lastInsertId(),
            "rating" => $rating,
            "comment" => $comment
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
