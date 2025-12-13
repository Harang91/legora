<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';
require_once __DIR__ . '/../shared/lego_helpers.php';

/**
 * get_cart.php
 * -------------
 * A bejelentkezett felhasználó kosarának lekérése.
 * - Csak bejelentkezett user hívhatja meg.
 * - Válasz: kosár tételek + összegzés (subtotal).
 * - Minden tételhez csatoljuk a listings adatait és a LEGO metaadatokat.
 * - Egységes JSON válasz formátum.
 */

// Csak GET kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak GET engedélyezett)");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$user_id = $_SESSION['user_id'];

try {
    // Kosár tételek lekérése JOIN-nal a listings és users táblára
    $stmt = $pdo->prepare("
        SELECT 
            c.id AS cart_item_id,
            c.quantity AS cart_quantity,
            c.added_at,
            l.id AS listing_id,
            l.item_type,
            l.item_id,
            l.price,
            l.item_condition,
            l.description,
            l.created_at,
            u.username AS seller
        FROM cart c
        JOIN listings l ON c.listing_id = l.id
        JOIN users u ON l.user_id = u.id
        WHERE c.user_id = ? AND l.deleted_at IS NULL
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $subtotal = 0.0;
    foreach ($items as &$item) {
        // LEGO metaadat csatolása
        $item['lego_data'] = getLegoData($pdo, $item['item_type'], $item['item_id']);

        // Sorösszeg számítás
        $lineTotal = ((float)$item['price']) * (int)$item['cart_quantity'];
        $item['line_total'] = number_format($lineTotal, 2, '.', '');
        $subtotal += $lineTotal;
    }
    $subtotal = number_format($subtotal, 2, '.', '');

    successResponse("Kosár lekérve", [
        "items" => $items,
        "summary" => [
            "subtotal" => $subtotal
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 

### Cél  
A `get_cart.php` endpoint feladata, hogy a **bejelentkezett felhasználó kosarát lekérje**.  
- Csak bejelentkezett user hívhatja meg.  
- Válasz: kosár tételek + összegzés (`subtotal`).  
- Minden tételhez csatolja a listings adatait és a LEGO metaadatokat (`lego_helpers.php`).  
- Egységes JSON válaszformátumot ad vissza.  

### Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Kosár tételekhez automatikusan csatolódnak a LEGO metaadatok → gazdagabb információ a felhasználónak.  
  - Vizsgán jól bemutatható → listanézet, összegzés, metaadatok, sorösszeg és subtotal számítás.  




régi kód
##Magyarázat

- **JOIN**: a `cart` táblát összekapcsoljuk a `listings` és `users` táblával, így minden kosár elemhez megkapjuk:
  - a hirdetés adatait (`item_type`, `item_id`, `price`, `description`, `item_condition`, `created_at`)  
  - az eladó nevét (`seller`)  
- **lego_data**: a `lego_helpers.php` segítségével minden kosár elemhez hozzácsatoljuk a LEGO metaadatokat.  
- **line_total**: minden sorhoz kiszámoljuk a mennyiség × ár értéket.  
- **subtotal**: a kosár teljes összege.  
- **Válasz**: a frontend egy `items` tömböt kap, benne minden kosár tétellel, és egy `summary` objektumot a teljes összeggel.  

---

*/