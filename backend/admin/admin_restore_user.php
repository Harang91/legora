<?php

require_once __DIR__ . '/../shared/init.php';

// Csak POST metódus engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// Admin session ellenőrzése
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

// Kötelező paraméter ellenőrzése
if (!$id) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználó azonosító (id).");
}

try {
    // Felhasználó lekérése
    $stmt = $pdo->prepare("
        SELECT id, username, is_active, role
        FROM users
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        errorResponse("Nem található felhasználó ezzel az ID-val.");
    }

    // Ha már aktív, nem állítható vissza
    if ((int)$user['is_active'] === 1) {
        http_response_code(409);
        errorResponse("A felhasználó nincs inaktiválva, így nem állítható vissza.");
    }

    // Soft delete visszavonása
    $stmtRestore = $pdo->prepare("
        UPDATE users
        SET is_active = 1
        WHERE id = :id
    ");
    $stmtRestore->execute(['id' => $id]);

    successResponse("Felhasználó sikeresen visszaállítva.", [
        "user_id" => (int)$id,
        "username" => $user['username']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
