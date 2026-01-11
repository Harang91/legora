<?php

require_once __DIR__ . '/../shared/init.php';

// Csak GET kérés engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak GET engedélyezett)");
}

// Kötelező paraméter
$rated_user_id = $_GET['rated_user_id'] ?? null;
if (!$rated_user_id) {
    http_response_code(422);
    errorResponse("Hiányzó rated_user_id paraméter");
}

try {
    // Értékelések lekérése
    $stmt = $pdo->prepare("
        SELECT 
            r.id AS rating_id,
            r.rating,
            r.comment,
            r.rated_at,
            u.username AS rater_username,
            u.id AS rater_id
        FROM ratings r
        JOIN users u ON r.rater_id = u.id
        WHERE r.rated_user_id = ?
        ORDER BY r.rated_at DESC
    ");
    $stmt->execute([$rated_user_id]);
    $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Átlagos értékelés
    $avg = null;
    if ($ratings) {
        $sum = 0;
        foreach ($ratings as $row) {
            $sum += (int)$row['rating'];
        }
        $avg = round($sum / count($ratings), 2);
    }

    successResponse("Értékelések lekérve", [
        "rated_user_id" => (int)$rated_user_id,
        "average_rating" => $avg,
        "total_ratings" => count($ratings),
        "ratings" => $ratings
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
