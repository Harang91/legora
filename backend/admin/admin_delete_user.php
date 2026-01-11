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
        SELECT id, username, role 
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

    
    if ($user['role'] === 'admin') {
        http_response_code(403);
        errorResponse("Admin felhasználó nem törölhető.");
    }

   
    $stmtDelete = $pdo->prepare("
        UPDATE users 
        SET is_active = 0 
        WHERE id = :id
    ");
    $stmtDelete->execute(['id' => $id]);

    successResponse("Felhasználó sikeresen inaktiválva.", [
        "user_id" => (int)$id,
        "username" => $user['username']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

