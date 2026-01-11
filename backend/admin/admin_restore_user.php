<?php

require_once __DIR__ . '/../shared/init.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}


if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}


$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
if (!$id) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználó azonosító (id).");
}

try {
   
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

    
    if ((int)$user['is_active'] === 1) {
        http_response_code(409);
        errorResponse("A felhasználó nincs inaktiválva, így nem állítható vissza.");
    }

    
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


