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

$username = trim($input['username'] ?? '');
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$captcha  = $input['captcha'] ?? '';

if ($username === '' || $email === '' || $password === '' || $captcha === '') {
    http_response_code(422);
    errorResponse("Hiányzó mezők");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    errorResponse("Hibás email");
}

$blockedEmails = ['teszt@teszt.com', 'asd@asd.com', 'example@example.com'];
if (in_array(strtolower($email), $blockedEmails)) {
    http_response_code(422);
    errorResponse("Ez az e‑mail cím nem engedélyezett");
}

if ($captcha !== '1234') {
    http_response_code(403);
    errorResponse("Hibás CAPTCHA");
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        http_response_code(409);
        errorResponse("E‑mail vagy felhasználónév foglalt");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $verifyToken = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password_hash, is_active, verify_token)
        VALUES (?, ?, ?, 0, ?)
    ");
    $stmt->execute([$username, $email, $hashedPassword, $verifyToken]);

    $verifyLink = "http://localhost/legora/auth/verify.php?token=" . urlencode($verifyToken);

    http_response_code(201);
    successResponse("Regisztráció sikeres", [
        "user_id"     => $pdo->lastInsertId(),
        "username"    => $username,
        "email"       => $email,
        "verify_link" => $verifyLink
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba");
}
