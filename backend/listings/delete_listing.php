<?php

require_once __DIR__ . '/../shared/init.php';

// Csak DELETE kérés engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak DELETE engedélyezett)");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges a hirdetés törléséhez");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$listing_id = (int)($input['listing_id'] ?? 0);

if ($listing_id <= 0) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó listing_id");
}

try {
    // Hirdetés ellenőrzése
    $stmt = $pdo->prepare("SELECT user_id, deleted_at FROM listings WHERE id = ?");
    $stmt->execute([$listing_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("A hirdetés nem található");
    }

    if ($listing['user_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        errorResponse("Nincs jogosultságod ennek a hirdetésnek a törlésére");
    }

    if ($listing['deleted_at'] !== null) {
        http_response_code(409);
        errorResponse("A hirdetés már törölve lett korábban");
    }

    // Logikai törlés
    $del = $pdo->prepare("UPDATE listings SET deleted_at = NOW() WHERE id = ? AND user_id = ?");
    $del->execute([$listing_id, $_SESSION['user_id']]);

    successResponse("Hirdetés sikeresen törölve (logikai törlés)", [
        "listing_id" => $listing_id,
        "deleted_at" => date('Y-m-d H:i:s')
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
