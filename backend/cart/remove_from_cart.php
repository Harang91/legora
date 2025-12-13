<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * remove_from_cart.php
 * ---------------------
 * Tétel mennyiségének csökkentése vagy teljes eltávolítása a kosárból.
 * - Csak bejelentkezett user hívhatja meg.
 * - Body: JSON { listing_id, quantity }
 * - Ha a meglévő mennyiség > quantity → frissítjük a mennyiséget.
 * - Ha a meglévő mennyiség <= quantity → teljes törlés a kosárból.
 * - Egységes JSON válasz formátum.
 */

// Csak DELETE kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak DELETE engedélyezett)");
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
    // Ellenőrizzük, hogy a tétel szerepel-e a kosárban
    $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND listing_id = ?");
    $check->execute([$user_id, $listing_id]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        http_response_code(404);
        errorResponse("A tétel nem található a kosárban");
    }

    $currentQty = (int)$existing['quantity'];

    if ($currentQty > $quantity) {
        // Csökkentjük a mennyiséget
        $newQty = $currentQty - $quantity;
        $upd = $pdo->prepare("UPDATE cart SET quantity = ?, added_at = NOW() WHERE id = ?");
        $upd->execute([$newQty, $existing['id']]);

        successResponse("Kosár tétel mennyisége csökkentve", [
            "cart_item_id" => (int)$existing['id'],
            "quantity" => $newQty
        ]);
    } else {
        // Ha a quantity <= meglévő mennyiség → teljes törlés
        $del = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $del->execute([$existing['id']]);

        successResponse("Tétel eltávolítva a kosárból", [
            "cart_item_id" => (int)$existing['id']
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 
### Cél  
A `remove_from_cart.php` endpoint feladata, hogy a **felhasználó kosarából csökkentse egy tétel mennyiségét vagy teljesen eltávolítsa azt**.  
- Csak bejelentkezett user hívhatja meg.  
- Kötelező paraméterek: `listing_id`, `quantity` (JSON body).  
- Ha a meglévő mennyiség nagyobb, mint a kért csökkentés → mennyiség frissítése.  
- Ha a meglévő mennyiség kisebb vagy egyenlő → teljes törlés a kosárból.  
- Egységes JSON válaszformátumot ad vissza.  

---

###  Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Kosár tételek kezelése egyszerű és átlátható: vagy frissítjük a mennyiséget, vagy töröljük a sort.  
  - Vizsgán jól bemutatható → DELETE metódus, feltételes logika, biztonságos adatkezelés.  


régikód
## Magyarázat

- **Autentikáció**: csak bejelentkezett user hívhatja meg.  
- **Request body**: `listing_id` és `quantity` szükséges.  
- **Ha a kosárban lévő mennyiség nagyobb** a kért csökkentésnél → frissítjük a mennyiséget.  
- **Ha a kosárban lévő mennyiség kisebb vagy egyenlő** → teljesen töröljük a sort a kosárból.  
- **Válasz**: mindig visszaadjuk a `cart_item_id`‑t és az új mennyiséget (vagy törlés esetén csak az ID‑t).  
*/