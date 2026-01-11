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
        SELECT id, username, is_active 
        FROM users 
        WHERE id = :id
    ");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        errorResponse("Nem található felhasználó ezzel az ID-val.");
    }

    
    $newStatus = $user['is_active'] == 1 ? 0 : 1;


    $updateStmt = $pdo->prepare("
        UPDATE users 
        SET is_active = :newStatus 
        WHERE id = :id
    ");
    $updateStmt->execute([
        'newStatus' => $newStatus,
        'id' => $userId
    ]);

    successResponse(
        $newStatus == 1 ? "A felhasználó aktiválva lett." : "A felhasználó inaktiválva lett.",
        [
            "user_id" => (int)$userId,
            "username" => $user['username'],
            "is_active" => $newStatus
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


