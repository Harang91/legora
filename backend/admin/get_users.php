<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_users.php
 * -------------------------
 * Általános funkció: összes felhasználó listázása.
 * - Csak GET metódus engedélyezett.
 * - Nem igényel admin session.
 * - Lekérdezi a users táblát, és visszaadja a fő adataikat.
 * - JSON választ ad vissza: success vagy error.
 */

// Csak GET kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Csak GET metódus engedélyezett.");
}

try {
    // Lekérdezzük az összes felhasználót
    $stmt = $pdo->query("
        SELECT id, username, email, role, is_active, created_at, address, phone
        FROM users
        ORDER BY created_at DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    successResponse("Felhasználók listája lekérve.", [
        "count" => count($users),
        "users" => $users
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 
### Cél  
A `get_users.php` endpoint feladata, hogy **listázza az összes felhasználót** (általános user lista, nem csak admin nézet).  
- Csak GET metódus engedélyezett.  
- Nem igényel admin session (általános lekérés).  
- Lekérdezi a `users` táblát, és visszaadja a fő adataikat: `id, username, email, role, is_active, created_at, address, phone`.  
- JSON formátumban adja vissza az adatokat.  
- Hibakezelést tartalmaz.  

###  Összegzés
- **Mi változott?**
  - Javítva a hibás mezőnevek (`isactive` → `is_active`, `createdat` → `created_at`).  
  - Beépítve a metódus ellenőrzés (`$_SERVER['REQUEST_METHOD']`).  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`405`, `500`).  

- **Miért jobb így?**
  - Biztonságos → csak GET metódus engedélyezett.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - A mezőnevek konzisztenssé váltak az adatbázisban.  
  - Vizsgán jól bemutatható → REST alapelvek, hibakódok, listázás.  




Régi:
Összefoglalás
Ez a get_users.php script az admin modul része.  
- Kapcsolódik az adatbázishoz (users tábla).  
- Lekérdezi az összes felhasználót, és visszaadja a fő adataikat: id, username, email, role, isactive, createdat, address, phone.  
- Az eredményt JSON formátumban adja vissza, így a frontend könnyen meg tudja jeleníteni (pl. admin felületen egy táblázatban).  
- Hibakezelést is tartalmaz, így ha az adatbázisban hiba van, akkor status: error üzenetet küld.  

Ez tehát az admin modulban az összes felhasználó listázására szolgáló endpoint, amivel az admin át tudja tekinteni a rendszerben lévő usereket.  


*/