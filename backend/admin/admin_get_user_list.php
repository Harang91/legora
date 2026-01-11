<?php

require_once __DIR__ . '/../shared/init.php';


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Csak GET metódus engedélyezett.");
}


if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}

try {
   
    $stmt = $pdo->query("
        SELECT id, username, email, role, is_active 
        FROM users 
        ORDER BY id ASC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    successResponse("Felhasználók listája lekérve.", [
        "users" => $users
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

