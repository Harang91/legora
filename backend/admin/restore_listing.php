<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * restore_listing.php (admin modul)
 * -------------------------
 * Admin funkció: törölt hirdetés visszaállítása (soft delete undo).
 * - Csak POST metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Ellenőrzi, hogy a hirdetés létezik-e és törölt állapotban van-e.
 * - Visszaállítja a hirdetést (deleted_at = NULL).
 * - JSON választ ad vissza: success vagy error.
 */

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// Ellenőrizzük, hogy van-e aktív admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}

// Paraméter ellenőrzése
$listingId = $_POST['id'] ?? null;
if (!$listingId) {
    http_response_code(422);
    errorResponse("Hiányzik a hirdetés azonosító (id).");
}

try {
    // Ellenőrizzük, hogy létezik-e a hirdetés és törölt állapotban van-e
    $stmt = $pdo->prepare("
        SELECT id, title, deleted_at 
        FROM listings 
        WHERE id = :id 
        LIMIT 1
    ");
    $stmt->execute(['id' => $listingId]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("Nem található hirdetés ezzel az ID-val.");
    }

    if ($listing['deleted_at'] === null) {
        http_response_code(409);
        errorResponse("A hirdetés nincs törölt állapotban, így nem állítható vissza.");
    }

    // Visszaállítás: deleted_at mező NULL-ra állítása
    $stmtRestore = $pdo->prepare("
        UPDATE listings 
        SET deleted_at = NULL 
        WHERE id = :id
    ");
    $stmtRestore->execute(['id' => $listingId]);

    successResponse("Hirdetés sikeresen visszaállítva.", [
        "listing_id" => (int)$listingId,
        "title" => $listing['title']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/*
### Cél  
A `restore_listing.php` az **admin modul** része, amelynek feladata, hogy az adminisztrátor soft delete művelettel törölt hirdetéseket visszaállíthasson.  

- **Csak POST metódus engedélyezett**, mivel módosító műveletről van szó.  
- **Admin session ellenőrzés** szükséges, hogy csak bejelentkezett admin férhessen hozzá.  
- **Hirdetés ellenőrzése**: megnézi, hogy létezik‑e a hirdetés és valóban törölt állapotban van‑e.  
- **Visszaállítás megvalósítása**: a `deleted_at` mezőt `NULL`‑ra állítja, így a hirdetés újra aktív lesz.  
- **JSON válasz**: minden esetben egységes választ ad vissza (`success` vagy `error` státusz, részletes üzenettel).  


### Összegzés
- **Mi változott?**
  - Beépítve az admin session ellenőrzés (`$_SESSION['admin_id']`).  
  - A hirdetés ellenőrzése: csak akkor állítható vissza, ha valóban törölt állapotban van.  
  - Egységes hibakezelés (`errorResponse()`, `successResponse()`).  
  - HTTP státuszkódok pontosabb használata (`401`, `404`, `405`, `409`, `422`, `500`).  

- **Miért jobb így?**
  - Biztonságos → csak admin férhet hozzá.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - REST alapelveknek megfelelő → metódus, státuszkódok, hibakezelés.  
  - Vizsgán jól bemutatható → soft delete visszaállítás admin jogosultsággal.






Régi:
- admin/restore_listing.php, ami visszaállítja a törölt hirdetést (deleted_at=NULL). Így az admin modulod teljes lesz: törlés → listázás → visszaállítás

Az admin modulban soft delete megoldást használok. A delete_listing.php csak beállítja a deleted_at mezőt, a get_deleted_listings.php listázza a törölt hirdetéseket, és a restore_listing.php visszaállítja őket. Így az adatok nem vesznek el, hanem bármikor visszaállíthatók.


- Cél: az admin visszaállíthat  bármelyik törölt hirdetést.  
- Metódus: POST.  
- Session/jogosultság ellenőrzés: nincs explicit ellenőrzés a kódban (feltételezhető, hogy az admin modulban már eleve csak admin fér hozzá).  
- Egyszerűbb logika: csak az id alapján visszaállítja, ha törölve volt.  
- Tipikus használat: moderáció → pl. egy user törölte a hirdetését, de az admin vissza akarja állítani (pl. téves törlés, vagy szabályzat miatt).

- Admin restore → bármely hirdetés visszaállítása (moderációs jogkör). 


*/