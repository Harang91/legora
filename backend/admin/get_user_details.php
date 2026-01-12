<?php

require_once __DIR__ . '/../shared/init.php';

// Csak GET metódus engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Csak GET metódus engedélyezett.");
}

// Admin session ellenőrzése
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}

// Kötelező paraméter ellenőrzése
$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználó azonosító (id).");
}

try {
    // Felhasználó lekérése
    $stmt = $pdo->prepare("
        SELECT id, username, email, role, is_active, created_at
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

    // Felhasználó hirdetései
    $stmtListings = $pdo->prepare("
        SELECT id, item_type, item_id, price, item_condition, deleted_at
        FROM listings
        WHERE user_id = :id
    ");
    $stmtListings->execute(['id' => $id]);
    $listings = $stmtListings->fetchAll(PDO::FETCH_ASSOC);

    // Válasz összeállítása
    successResponse("Felhasználó részletes adatai lekérve.", [
        "user" => $user,
        "listings" => $listings
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
