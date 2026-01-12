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
    // Globális statisztikák
    $activeListings = (int)$pdo->query("SELECT COUNT(*) FROM listings WHERE deleted_at IS NULL")->fetchColumn();
    $deletedListings = (int)$pdo->query("SELECT COUNT(*) FROM listings WHERE deleted_at IS NOT NULL")->fetchColumn();
    $activeUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
    $inactiveUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 0")->fetchColumn();
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // Felhasználónkénti hirdetés statisztika
    $stmtUserStats = $pdo->query("
        SELECT u.id, u.username, u.email, u.is_active,
               COUNT(l.id) AS total_listings,
               SUM(CASE WHEN l.deleted_at IS NULL THEN 1 ELSE 0 END) AS active_listings,
               SUM(CASE WHEN l.deleted_at IS NOT NULL THEN 1 ELSE 0 END) AS deleted_listings
        FROM users u
        LEFT JOIN listings l ON u.id = l.user_id
        GROUP BY u.id, u.username, u.email, u.is_active
        ORDER BY total_listings DESC
    ");
    $userStats = $stmtUserStats->fetchAll(PDO::FETCH_ASSOC);

    // Hirdetés ár statisztika
    $stmtListingStats = $pdo->query("
        SELECT 
            COUNT(*) AS total_listings,
            AVG(price) AS avg_price,
            MIN(price) AS min_price,
            MAX(price) AS max_price
        FROM listings
    ");
    $listingStats = $stmtListingStats->fetch(PDO::FETCH_ASSOC);

    successResponse("Komplex statisztikák sikeresen lekérve.", [
        'global_stats' => [
            'active_listings' => $activeListings,
            'deleted_listings' => $deletedListings,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'total_users' => $totalUsers
        ],
        'user_stats' => $userStats,
        'listing_stats' => $listingStats
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
