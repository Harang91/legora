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
    // Aktív hirdetések száma
    $activeListings = (int)$pdo->query("SELECT COUNT(*) FROM listings WHERE deleted_at IS NULL")->fetchColumn();

    // Törölt hirdetések száma
    $deletedListings = (int)$pdo->query("SELECT COUNT(*) FROM listings WHERE deleted_at IS NOT NULL")->fetchColumn();

    // Aktív felhasználók száma
    $activeUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();

    // Inaktív felhasználók száma
    $inactiveUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 0")->fetchColumn();

    // Összes felhasználó száma
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    successResponse("Statisztikák sikeresen lekérve.", [
        'active_listings' => $activeListings,
        'deleted_listings' => $deletedListings,
        'active_users' => $activeUsers,
        'inactive_users' => $inactiveUsers,
        'total_users' => $totalUsers
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
