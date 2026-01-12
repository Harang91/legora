<?php

require_once __DIR__ . '/../shared/init.php';

// Csak POST metódus engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    errorResponse("Érvénytelen adatok (JSON várt).");
}

$username = $input['username'] ?? null;
$password = $input['password'] ?? null;

// Kötelező mezők ellenőrzése
if (!$username || !$password) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználónév vagy jelszó.");
}

try {
    // Felhasználó lekérése
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

    // Admin jogosultság ellenőrzése
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        errorResponse("Nincs admin jogosultság.");
    }

    // Aktív státusz ellenőrzése
    if ((int)$user['is_active'] === 0) {
        http_response_code(403);
        errorResponse("A felhasználó inaktív.");
    }

    // Jelszó ellenőrzése
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        errorResponse("Hibás jelszó.");
    }

    // Admin session beállítása
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
