<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * restore_listing.php
 * -------------------
 * Logikailag törölt hirdetés visszaállítása.
 * - Csak PUT/PATCH kérést enged.
 * - Csak bejelentkezett felhasználó vagy admin állíthatja vissza.
 * - Ellenőrzi, hogy a hirdetés létezik és törölt állapotban van.
 * - Egységes JSON válasz formátum.
 */

// Csak PUT vagy PATCH kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak PUT/PATCH engedélyezett)");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges a hirdetés visszaállításához");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$listing_id = (int)($input['listing_id'] ?? 0);

if ($listing_id <= 0) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó listing_id");
}

try {
    // Ellenőrizzük, hogy a hirdetés létezik
    $stmt = $pdo->prepare("SELECT user_id, deleted_at FROM listings WHERE id = ?");
    $stmt->execute([$listing_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("A hirdetés nem található");
    }

    // Jogosultság ellenőrzés (saját hirdetés vagy admin)
    if ($listing['user_id'] != $_SESSION['user_id'] && ($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        errorResponse("Nincs jogosultságod ennek a hirdetésnek a visszaállítására");
    }

    // Ha nincs törölve, nem lehet visszaállítani
    if ($listing['deleted_at'] === null) {
        http_response_code(409);
        errorResponse("A hirdetés nincs törölve, nem szükséges visszaállítani");
    }

    // Visszaállítás
    $upd = $pdo->prepare("UPDATE listings SET deleted_at = NULL WHERE id = ?");
    $upd->execute([$listing_id]);

    // Sikeres válasz
    successResponse("Hirdetés sikeresen visszaállítva", [
        "listing_id" => $listing_id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}





/* 


### Cél  
A `restore_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó (vagy admin) **visszaállíthassa a logikailag törölt hirdetését**.  
- Csak **PUT/PATCH** kérést enged.  
- Csak a **saját hirdetését** állíthatja vissza a user, vagy admin jogosultsággal bármely hirdetést.  
- Ellenőrzi, hogy a hirdetés létezik, valóban törölt, és jogosult a visszaállításra.  
- Ha a hirdetés nincs törölve, hibát ad.  
- Egységes hibakezelést és válaszformátumot használ.  


###  Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → REST szabványos működés, jogosultság ellenőrzés, részletes hibakódok.  



- Cél: a felhasználó visszaállíthatja a saját törölt hirdetését.  
- Metódus: PUT vagy PATCH.  
- session ellenőrzés: csak bejelentkezett user használhatja.  
- Jogosultság: ellenőrzi, hogy a hirdetés a sajátja-e, vagy az aktuális user admin.  
- Extra logika:  
  - Ha a hirdetés nincs törölve (deleted_at IS NULL), hibát ad.  
  - Ha nem a saját hirdetés, és nem admin, hibát ad.  
- Tipikus használat: egy user törölte a hirdetését, majd meggondolja magát → visszaállítja.

- User restore → saját hirdetés visszaállítása (biztonsági ellenőrzésekkel).

*/