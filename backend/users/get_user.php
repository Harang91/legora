<?php

require_once __DIR__ . '/../shared/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés csak GET engedélyezett");
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

try {
    $stmt = $pdo->prepare("
        SELECT id, username, email, created_at, address, phone
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(404);
        errorResponse("Felhasználó nem található");
    }

    successResponse("Felhasználói adatok betöltve", $user);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba");
}
