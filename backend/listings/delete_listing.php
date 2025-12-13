<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * delete_listing.php
 * ------------------
 * Logikai törlés egy meglévő hirdetésnél.
 * - Csak DELETE kérést enged.
 * - Csak bejelentkezett felhasználó törölheti a saját hirdetését.
 * - Nem fizikai törlés, hanem a deleted_at mező kitöltése.
 * - Egységes JSON válasz formátum.
 */

// Csak DELETE kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak DELETE engedélyezett)");
}

// Ellenőrizzük, hogy be van-e jelentkezve a felhasználó
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges a hirdetés törléséhez");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$listing_id = (int)($input['listing_id'] ?? 0);

if ($listing_id <= 0) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó listing_id");
}

try {
    // Ellenőrizzük, hogy a hirdetés létezik és a bejelentkezett useré
    $stmt = $pdo->prepare("SELECT user_id, deleted_at FROM listings WHERE id = ?");
    $stmt->execute([$listing_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("A hirdetés nem található");
    }

    if ($listing['user_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        errorResponse("Nincs jogosultságod ennek a hirdetésnek a törlésére");
    }

    if ($listing['deleted_at'] !== null) {
        http_response_code(409);
        errorResponse("A hirdetés már törölve lett korábban");
    }

    // Logikai törlés
    $del = $pdo->prepare("UPDATE listings 
                          SET deleted_at = NOW() 
                          WHERE id = ? AND user_id = ?");
    $del->execute([$listing_id, $_SESSION['user_id']]);

    // Sikeres válasz
    successResponse("Hirdetés sikeresen törölve (logikai törlés)", [
        "listing_id" => $listing_id,
        "deleted_at" => date('Y-m-d H:i:s')
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/* 

### Cél  
A `delete_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó **logikailag törölje a saját hirdetését**.  
- Csak **DELETE** kérést enged.  
- Csak a **saját hirdetését** törölheti a user.  
- Nem fizikai törlés történik, hanem a `deleted_at` mező kitöltése → így később visszaállítható (`restore_listing.php`).  
- Ellenőrzi, hogy a hirdetés létezik, nem törölt, és valóban a bejelentkezett userhez tartozik.  
- Egységes hibakezelést és válaszformátumot használ.  


### Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → REST szabványos működés, logikai törlés, részletes hibakódok.  






Mit tud ez az endpoint?
- Csak DELETE metódust enged.  
- Csak bejelentkezett felhasználó törölhet.  
- Ellenőrzi, hogy a hirdetés valóban az adott userhez tartozik.  
- Ha minden rendben, törli a rekordot a `listings` táblából.  
- Egységes JSON választ ad vissza.

*/