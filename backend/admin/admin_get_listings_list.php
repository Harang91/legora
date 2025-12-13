<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * admin_get_listings_list.php
 * ----------------------------
 * Admin funkció: összes hirdetés listázása.
 * - Csak GET metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Visszaadja az összes hirdetést (aktív és soft delete-elt).
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
    // Lekérdezzük az összes hirdetést (aktív és törölt)
    $stmt = $pdo->query("
        SELECT id, title, description, price, user_id, created_at, deleted_at
        FROM listings
        ORDER BY id ASC
    ");
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    successResponse("Hirdetések listája lekérve.", [
        "listings" => $listings
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/* 

### Cél  
Az `admin_get_listings_list.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes hirdetést**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja az összes hirdetést (aktív és soft delete‑elt).  
- JSON formátumban adja vissza az adatokat.  
- Hibakezelést tartalmaz minden tipikus esetre.  


###  Összegzés
- **Mi változott?**
  - Javítva a hibás változó (`$SERVER` → `$_SERVER`).  
  - Javítva a mezőnevek (`userid` → `user_id`, `createdat` → `created_at`).  
  - Beépítve az admin session ellenőrzés → csak bejelentkezett admin férhet hozzá.  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`401`, `405`, `500`).  

- **Miért jobb így?**
  - Biztonságos → csak admin jogosultsággal érhető el.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - Valós adatbázis mezők → a dokumentáció és tesztek életszerűek lesznek.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok, listázás.  





RÉGI:
Kommentek a kódhoz
- Csak GET metódus engedélyezett → így biztonságosabb, mert listázásra használjuk.  
- Lekérdezés → minden hirdetést visszaad, beleértve azokat is, amelyek soft delete‑elve vannak (deleted_at mező nem NULL).  
- JSON válasz → status mező jelzi a sikerességet, a listings tömb tartalmazza az adatokat.  
- Hibakezelés → ha adatbázis hiba történik, error státuszt ad vissza.

---

Összefoglalás
Ez az admin_get_listings_list.php script az admin modulban az összes hirdetés listázását kezeli:  
- Csak GET metódus engedélyezett.  
- Visszaadja az összes hirdetést (aktív és törölt).  
- JSON formátumban adja vissza az adatokat, így a frontend könnyen meg tudja jeleníteni.  
- Hibakezelést tartalmaz minden tipikus esetre.  

Ez  jól demonstrálja a hirdetéskezelés áttekintő funkcióját.

*/