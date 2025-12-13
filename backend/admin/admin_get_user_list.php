<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * admin_get_user_list.php
 * ------------------------
 * Admin funkció: összes felhasználó listázása.
 * - Csak GET metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Visszaadja az összes felhasználót (aktív és inaktív).
 * - JSON formátumban adja vissza az adatokat.
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
    // Lekérdezzük az összes felhasználót
    $stmt = $pdo->query("
        SELECT id, username, email, role, is_active 
        FROM users 
        ORDER BY id ASC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    successResponse("Felhasználók listája lekérve.", [
        "users" => $users
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 
### Cél  
Az `admin_get_user_list.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes felhasználót**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja az összes felhasználót (aktív és inaktív).  
- JSON formátumban adja vissza az adatokat, így a frontend könnyen meg tudja jeleníteni.  

---
###  Összegzés
- **Mi változott?**
  - Javítva a hibás változó (`$SERVER` → `$_SERVER`).  
  - Beépítve az admin session ellenőrzés → csak bejelentkezett admin férhet hozzá.  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`401`, `405`, `500`).  

- **Miért jobb így?**
  - Biztonságos → csak admin jogosultsággal érhető el.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok, listázás.



RÉGI:
Összefoglalás
Ez az admin_get_user_list.php script az admin modulban az összes felhasználó listázását kezeli:  
- Csak GET metódus engedélyezett.  
- Visszaadja az összes felhasználót (aktív és inaktív).  
- JSON formátumban adja vissza az adatokat, így a frontend könnyen meg tudja jeleníteni.  

Ez jól demonstrálja a felhasználókezelés áttekintő funkcióját.
*/