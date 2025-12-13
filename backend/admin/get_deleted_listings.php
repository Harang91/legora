<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_deleted_listings.php
 * -------------------------
 * Admin funkció: törölt hirdetések listázása.
 * - Csak GET metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Lekérdezi a listings táblát, ahol deleted_at IS NOT NULL.
 * - JOIN-olja a users táblát, hogy látszódjon a hirdető neve és emailje.
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
    // Lekérdezzük a törölt hirdetéseket
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

/* 
### Cél  
A `get_deleted_listings.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes törölt (soft delete‑elt) hirdetést**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Lekérdezi a `listings` táblát, ahol `deleted_at IS NOT NULL`.  
- JOIN‑olja a `users` táblát, hogy látszódjon a hirdető neve és emailje.  
- JSON formátumban adja vissza az adatokat.  
- Hibakezelést tartalmaz minden tipikus esetre.  

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
  - A `users` tábla JOIN‑olása miatt a hirdető neve és emailje is látszik.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok, soft delete kezelés.  

---



Régi
- Lekérdezi a `listings` táblából azokat a rekordokat, ahol `deleted_at IS NOT NULL`.  
- Kiírja a hirdetés adatait (pl. id, user_id, item_type, item_id, price, description, deleted_at). 
- Az admin modulban egy gomb → „Törölt hirdetések” → és máris listázza a JSON‑t vagy HTML táblázatot.  
- Meg tudod mutatni, hogy a rendszer nem törli fizikailag a rekordot, hanem csak `deleted_at` mezőt állítja.
- Így az admin vissza tudja nézni, mikor és ki törölt hirdetést.

tehát:
- **Lekérdezi** a `listings` táblát, ahol `deleted_at IS NOT NULL`.  
- **JOIN‑olja** a `users` táblát, hogy látszódjon a hirdető neve és emailje.  
- **JSON‑ban adja vissza** az eredményt, így könnyen feldolgozható vagy megjeleníthető.  
- Hibakezelést is tartalmaz, ha valami gond van az adatbázis‑kapcsolattal.

*/