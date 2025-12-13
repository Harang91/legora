<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * add_to_cart.php
 * ----------------
 * Tétel hozzáadása a kosárhoz.
 * - Csak bejelentkezett user hívhatja meg.
 * - Body: JSON { listing_id, quantity }
 * - Ha a tétel már szerepel a kosárban, növeli a mennyiséget.
 * - Ha nincs, új rekordot hoz létre.
 * - Egységes JSON válasz formátum.
 */

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak POST engedélyezett)");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$user_id = $_SESSION['user_id'];

// Request body beolvasása
$input = json_decode(file_get_contents('php://input'), true);

$listing_id = $input['listing_id'] ?? null;
$quantity   = isset($input['quantity']) ? (int)$input['quantity'] : null;

// Alap validáció
if (!$listing_id || !$quantity || $quantity < 1) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó mezők");
}

try {
    // Ellenőrizzük, hogy a listing létezik és nem törölt
    $checkListing = $pdo->prepare("SELECT id, deleted_at FROM listings WHERE id = ?");
    $checkListing->execute([$listing_id]);
    $listing = $checkListing->fetch(PDO::FETCH_ASSOC);

    if (!$listing || $listing['deleted_at'] !== null) {
        http_response_code(404);
        errorResponse("A hirdetés nem található vagy törölve lett");
    }

    // Ellenőrizzük, hogy van-e már ilyen tétel a kosárban
    $checkCart = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND listing_id = ?");
    $checkCart->execute([$user_id, $listing_id]);
    $existing = $checkCart->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Frissítjük a mennyiséget
        $newQty = (int)$existing['quantity'] + $quantity;
        $upd = $pdo->prepare("UPDATE cart SET quantity = ?, added_at = NOW() WHERE id = ?");
        $upd->execute([$newQty, $existing['id']]);

        successResponse("Kosár tétel frissítve", [
            "cart_item_id" => (int)$existing['id'],
            "quantity" => $newQty
        ]);
    } else {
        // Új tétel beszúrása
        $ins = $pdo->prepare("INSERT INTO cart (user_id, listing_id, quantity, added_at)
                              VALUES (?, ?, ?, NOW())");
        $ins->execute([$user_id, $listing_id, $quantity]);

        successResponse("Tétel hozzáadva a kosárhoz", [
            "cart_item_id" => (int)$pdo->lastInsertId(),
            "quantity" => $quantity
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}




/* 
### Cél  
Az `add_to_cart.php` endpoint feladata, hogy a **felhasználó kosarához új tételt adjon** vagy frissítse a meglévő mennyiséget.  
- Csak bejelentkezett user hívhatja meg.  
- Kötelező paraméterek: `listing_id`, `quantity` (JSON body).  
- Ellenőrzi, hogy a hirdetés létezik és nincs törölve.  
- Ha a tétel már szerepel a kosárban → mennyiség növelése.  
- Ha nem szerepel → új rekord létrehozása.  
- Egységes JSON válaszformátumot ad vissza.  

###  Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → egyszerű, tiszta logika: validáció → ellenőrzés → frissítés vagy beszúrás → válasz.  







régi kód
## Magyarázat

- **Autentikáció**: csak bejelentkezett user hívhatja meg (`$_SESSION['user_id']`).  
- **Request body**: csak `listing_id` és `quantity` kell.  
- **Validáció**: ellenőrizzük, hogy a `listing_id` létezik a `listings` táblában, és nincs törölve (`deleted_at IS NULL`).  
- **Ha már van a kosárban**: frissítjük a mennyiséget (`quantity += új quantity`).  
- **Ha nincs a kosárban**: új rekordot szúrunk be.  
- **Válasz**: mindig visszaadjuk a `cart_item_id`‑t és az aktuális mennyiséget. 

*/