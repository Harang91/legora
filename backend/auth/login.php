<?php

require_once __DIR__ . '/../shared/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés");
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    errorResponse("Érvénytelen JSON");
}

$emailOrUsername = trim($input['email_or_username'] ?? '');
$password        = $input['password'] ?? '';

if ($emailOrUsername === '' || $password === '') {
    http_response_code(422);
    errorResponse("Hiányzó mezők");
}

try {
    $stmt = $pdo->prepare("
        SELECT id, username, email, password_hash, is_active
        FROM users
        WHERE email = ? OR username = ?
    ");
    $stmt->execute([$emailOrUsername, $emailOrUsername]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        errorResponse("Hibás felhasználónév vagy e‑mail");
    }

    if ((int)$user['is_active'] !== 1) {
        http_response_code(403);
        errorResponse("A fiók nincs aktiválva");
    }

    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        errorResponse("Hibás jelszó");
    }

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];

    successResponse("Sikeres bejelentkezés", [
        "user_id"  => $user['id'],
        "username" => $user['username'],
        "email"    => $user['email']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba");
}
