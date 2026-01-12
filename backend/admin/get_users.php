<?php

require_once __DIR__ . '/../shared/init.php';

// Csak GET metódus engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Csak GET metódus engedélyezett.");
}

try {
    // Összes felhasználó lekérése
    $stmt = $pdo->query("
        SELECT id, username, email, role, is_active, created_at, address, phone
        FROM users
        ORDER BY created_at DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    successResponse("Felhasználók listája lekérve.", [
        "count" => count($users),
        "users" => $users
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
