<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_all_stats.php (admin modul)
 * -------------------------
 * Admin funkció: komplex statisztikák lekérése.
 * - Csak GET metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Visszaadja a globális, felhasználói és hirdetés statisztikákat.
 * - JSON választ ad vissza: success vagy error.
 */

// Csak GET kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Csak GET metódus engedélyezett.");
}

// Ellenőrizzük, hogy van-e aktív admin session
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

    // Hirdetésenkénti összesítés (ár statisztika)
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


/* 
### Cél  
A `get_all_stats.php` az **admin modul** része, amely komplex statisztikákat ad vissza a rendszer állapotáról.  
- Csak **GET metódus** engedélyezett.  
- Admin session szükséges.  
- Visszaadja:  
  - Globális számok (aktív/törölt hirdetések, aktív/inaktív/összes user).  
  - Felhasználónkénti bontás: hány hirdetésük van, abból mennyi aktív/törölt.  
  - Hirdetésenkénti összesítés: átlagár, minimum, maximum.  
- JSON formátumban adja vissza az adatokat.  


###  Összegzés
- **Mi változott?**
  - Javítva a hibás változónév (`$SERVER['REQUESTMETHOD']` → `$_SERVER['REQUEST_METHOD']`).  
  - Beépítve az admin session ellenőrzés (`$_SESSION['admin_id']`).  
  - Egységes hibakezelés (`errorResponse()`, `successResponse()`).  
  - HTTP státuszkódok pontosabb használata (`401`, `405`, `500`).  
  - Javítva a mezőnevek (`deletedat` → `deleted_at`).  
  - Típusos konverzió: minden számláló integerként kerül visszaadásra.  

- **Miért jobb így?**
  - Biztonságos → csak admin férhet hozzá.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - REST alapelveknek megfelelő → metódus, státuszkódok, hibakezelés.  
  - Vizsgán jól bemutatható → komplex statisztikai összesítés (globális + user + listing).  

---



Régi:
Összefoglalás
Ez a get_all_stats.php script az admin modulban komplex statisztikákat ad vissza:  
- Globális számok (aktív/törölt hirdetések, aktív/inaktív/összes user).  
- Felhasználónkénti bontás: hány hirdetésük van, abból mennyi aktív/törölt.  
- Hirdetésenkénti összesítés: átlagár, minimum, maximum.  

Ez mutatja, hogy az admin modul nemcsak kezel, hanem elemez is.
*/