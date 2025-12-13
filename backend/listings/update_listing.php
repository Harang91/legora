<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * update_listing.php
 * ------------------
 * Meglévő hirdetés frissítése.
 * - Csak PUT/PATCH kérést enged.
 * - Csak bejelentkezett felhasználó módosíthatja a saját hirdetését.
 * - Csak bizonyos mezők frissíthetők: quantity, price, item_condition, description.
 */

// Csak PUT vagy PATCH kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak PUT/PATCH engedélyezett)");
}

// Ellenőrizzük, hogy be van-e jelentkezve a felhasználó
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges a hirdetés módosításához");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$listing_id = (int)($input['listing_id'] ?? 0);

if ($listing_id <= 0) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó listing_id");
}

// Megengedett mezők frissítéshez
$allowed_fields = ['quantity', 'price', 'item_condition', 'description'];
$updates = [];
$params = [];

foreach ($allowed_fields as $field) {
    if (isset($input[$field])) {
        $updates[] = "$field = ?";
        $params[] = $input[$field];
    }
}

if (empty($updates)) {
    http_response_code(422);
    errorResponse("Nincs frissíthető mező megadva");
}

try {
    // Ellenőrizzük, hogy a hirdetés létezik és a useré
    $stmt = $pdo->prepare("SELECT user_id, deleted_at FROM listings WHERE id = ?");
    $stmt->execute([$listing_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("A hirdetés nem található");
    }

    if ($listing['user_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        errorResponse("Nincs jogosultságod ennek a hirdetésnek a módosítására");
    }

    if ($listing['deleted_at'] !== null) {
        http_response_code(409);
        errorResponse("A hirdetés már törölve lett, nem módosítható");
    }

    // UPDATE futtatása
    $sql = "UPDATE listings SET " . implode(", ", $updates) . " WHERE id = ? AND user_id = ?";
    $params[] = $listing_id;
    $params[] = $_SESSION['user_id'];

    $upd = $pdo->prepare($sql);
    $upd->execute($params);

    // Sikeres válasz
    successResponse("Hirdetés sikeresen frissítve", [
        "listing_id" => $listing_id,
        "updated_fields" => array_keys(array_intersect_key($input, array_flip($allowed_fields)))
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/* 


### Cél  
A `update_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó **módosíthassa a saját hirdetését**.  
- Csak **PUT/PATCH** kérést enged.  
- Csak a **saját hirdetését** frissítheti a user.  
- Csak bizonyos mezők frissíthetők: `quantity`, `price`, `item_condition`, `description`.  
- Ellenőrzi, hogy a hirdetés létezik, nem törölt, és valóban a bejelentkezett userhez tartozik.  
- Dinamikusan építi az SQL-t, így csak a megadott mezők frissülnek.  
- Egységes hibakezelést és válaszformátumot használ.  


### Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → REST szabványos működés, dinamikus SQL, részletes hibakódok.  

---










Ez a script az **user által létrehozott hirdetés frissítését** kezeli.

---

##  Összefoglaló – `update_listing.php`

###  Funkció
Ez az endpoint lehetővé teszi, hogy egy bejelentkezett felhasználó **módosítsa a saját hirdetését**.  
Csak bizonyos mezők frissíthetők: `quantity`, `price`, `item_condition`, `description`.

---



### Biztonsági megoldások
- **Session alapú jogosultság**: csak bejelentkezett user módosíthat.  
- **Tulajdonjog ellenőrzés**: csak a saját hirdetését frissítheti.  
- **Logikai törlés kezelése**: törölt hirdetés nem módosítható.  
- **Paraméterezett SQL**: védelem SQL injection ellen.  




*/