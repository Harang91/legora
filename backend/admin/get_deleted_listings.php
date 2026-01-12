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

try {
    // Törölt hirdetések lekérése eladó adataival
    $stmt = $pdo->prepare("
        SELECT l.id, l.user_id, l.item_type, l.item_id, l.price, l.item_condition,
               l.description, l.deleted_at, u.username, u.email
        FROM listings l
        JOIN users u ON l.user_id = u.id
        WHERE l.deleted_at IS NOT NULL
        ORDER BY l.deleted_at DESC
    ");
    $stmt->execute();
    $deletedListings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    successResponse("Törölt hirdetések listája lekérve.", [
        "count" => count($deletedListings),
        "listings" => $deletedListings
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
