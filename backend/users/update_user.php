<?php

require_once __DIR__ . '/../shared/init.php';

// Csak PUT/PATCH kérés engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

// JSON beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$fields = [];
$params = [];

// Frissíthető mezők validálása
if (isset($input['email'])) {
    if (!validateEmail($input['email'])) {
        http_response_code(422);
        errorResponse("Hibás email formátum");
    }
    $fields[] = "email = ?";
    $params[] = $input['email'];
}

if (isset($input['username'])) {
    $fields[] = "username = ?";
    $params[] = $input['username'];
}

if (isset($input['password'])) {
    if (!validatePassword($input['password'])) {
        http_response_code(422);
        errorResponse("Gyenge jelszó");
    }
    $fields[] = "password_hash = ?";
    $params[] = password_hash($input['password'], PASSWORD_BCRYPT);
}

if (isset($input['address'])) {
    if (!validateAddress($input['address'])) {
        http_response_code(422);
        errorResponse("Hibás lakcím");
    }
    $fields[] = "address = ?";
    $params[] = $input['address'];
}

if (isset($input['phone'])) {
    if (!validatePhone($input['phone'])) {
        http_response_code(422);
        errorResponse("Hibás telefonszám");
    }
    $fields[] = "phone = ?";
    $params[] = $input['phone'];
}

if (empty($fields)) {
    http_response_code(422);
    errorResponse("Nincs frissíthető mező");
}

$params[] = $_SESSION['user_id'];

try {
    $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    successResponse("Felhasználói adatok frissítve", [
        "updated_fields" => array_keys($input)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba");
}
