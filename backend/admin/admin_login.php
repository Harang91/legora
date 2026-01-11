<?php

require_once __DIR__ . '/../shared/init.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}


$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    errorResponse("Érvénytelen adatok (JSON várt).");
}

$username = $input['username'] ?? null;
$password = $input['password'] ?? null;

if (!$username || !$password) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználónév vagy jelszó.");
}

try {
    
    $stmt = $pdo->prepare("
        SELECT id, username, password_hash, role, is_active 
        FROM users 
        WHERE username = :username 
        LIMIT 1
    ");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        errorResponse("Nincs ilyen felhasználó.");
    }

    if ($user['role'] !== 'admin') {
        http_response_code(403);
        errorResponse("Nincs admin jogosultság.");
    }

    if ((int)$user['is_active'] === 0) {
        http_response_code(403);
        errorResponse("A felhasználó inaktív.");
    }

    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        errorResponse("Hibás jelszó.");
    }

    
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];

    successResponse("Sikeres admin bejelentkezés.", [
        "admin_id" => (int)$user['id'],
        "username" => $user['username']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}