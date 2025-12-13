<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_stats.php (admin modul)
 * -------------------------
 * Admin funkció: összesítő statisztikák lekérése.
 * - Csak GET metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Visszaadja az aktív/törölt hirdetések és felhasználók számát.
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
    // Aktív hirdetések száma
    $stmtActiveListings = $pdo->query("SELECT COUNT(*) FROM listings WHERE deleted_at IS NULL");
    $activeListings = (int)$stmtActiveListings->fetchColumn();

    // Törölt hirdetések száma
    $stmtDeletedListings = $pdo->query("SELECT COUNT(*) FROM listings WHERE deleted_at IS NOT NULL");
    $deletedListings = (int)$stmtDeletedListings->fetchColumn();

    // Aktív felhasználók száma
    $stmtActiveUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1");
    $activeUsers = (int)$stmtActiveUsers->fetchColumn();

    // Inaktív felhasználók száma
    $stmtInactiveUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 0");
    $inactiveUsers = (int)$stmtInactiveUsers->fetchColumn();

    // Összes felhasználó száma
    $stmtTotalUsers = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = (int)$stmtTotalUsers->fetchColumn();

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

/* 
### Cél  
A `get_stats.php` az **admin modul** része, amely összesítő statisztikákat ad vissza a rendszer állapotáról.  
- Csak **GET metódus** engedélyezett (lekérdezés).  
- Admin session szükséges.  
- Visszaadja:  
  - Aktív hirdetések száma  
  - Törölt hirdetések száma  
  - Aktív felhasználók száma  
  - Inaktív felhasználók száma  
  - Összes felhasználó száma  
- JSON formátumban adja vissza az adatokat.  

### Összegzés
- **Mi változott?**
  - Beépítve a metódus ellenőrzés (`$_SERVER['REQUEST_METHOD']`).  
  - Admin session ellenőrzés (`$_SESSION['admin_id']`).  
  - Egységes hibakezelés (`errorResponse()`, `successResponse()`).  
  - HTTP státuszkódok pontosabb használata (`401`, `405`, `500`).  
  - Típusos konverzió: minden számláló integerként kerül visszaadásra.  

- **Miért jobb így?**
  - Biztonságos → csak admin férhet hozzá.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - REST alapelveknek megfelelő → metódus, státuszkódok, hibakezelés.  
  - Vizsgán jól bemutatható → dashboard statisztikák admin panelhez.  




Régi:
Összefoglalás
Ez a get_stats.php script az admin modulban összesítő statisztikákat ad vissza:  
- Aktív hirdetések száma  
- Törölt hirdetések száma  
- Aktív felhasználók száma  
- Inaktív felhasználók száma  
- Összes felhasználó száma  

Az eredményt JSON formátumban adja vissza, így a frontend könnyen meg tudja jeleníteni egy dashboardon vagy admin panelen.  
Az admin modul így nem csak kezel, hanem áttekintést is ad a rendszer állapotáról.

*/