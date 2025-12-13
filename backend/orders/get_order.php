<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_order.php
 * ----------------
 * Egy adott rendelés részleteinek lekérdezése.
 * - Csak bejelentkezett user hívhatja meg.
 * - Kötelező paraméter: order_id (?id=123).
 * - Jogosultság: csak a buyer vagy a seller láthatja.
 * - Lekérdezés: order alapadatok + order_items + order_status_history.
 * - Egységes JSON válasz formátum.
 */

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  errorResponse("Bejelentkezés szükséges");
}

$user_id = $_SESSION['user_id'];

// order_id paraméter kötelező
if (!isset($_GET['id'])) {
  http_response_code(400);
  errorResponse("Hiányzó order_id paraméter");
}

$order_id = (int)$_GET['id'];

try {
  // 1. Lekérdezzük az order alapadatait
  $sqlOrder = "
        SELECT 
            o.id AS order_id,
            o.buyer_id,
            buyer.username AS buyer_name,
            o.seller_id,
            seller.username AS seller_name,
            o.total_price,
            o.status,
            o.ordered_at
        FROM orders o
        JOIN users buyer ON o.buyer_id = buyer.id
        JOIN users seller ON o.seller_id = seller.id
        WHERE o.id = ?
          AND (o.buyer_id = ? OR o.seller_id = ?)
    ";
  $stmt = $pdo->prepare($sqlOrder);
  $stmt->execute([$order_id, $user_id, $user_id]);
  $order = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$order) {
    http_response_code(404);
    errorResponse("Nincs ilyen rendelés, vagy nincs jogosultságod megtekinteni");
  }

  // 2. Lekérdezzük a rendelés tételeit
  $sqlItems = "
        SELECT 
            oi.id AS order_item_id,
            oi.listing_id,
            l.title AS listing_title,
            oi.quantity,
            oi.price_at_order
        FROM order_items oi
        JOIN listings l ON oi.listing_id = l.id
        WHERE oi.order_id = ?
    ";
  $stmtItems = $pdo->prepare($sqlItems);
  $stmtItems->execute([$order_id]);
  $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

  // 3. Lekérdezzük a státusztörténetet
  $sqlHistory = "
        SELECT 
            h.old_status,
            h.new_status,
            h.changed_at,
            u.username AS changed_by
        FROM order_status_history h
        JOIN users u ON h.changed_by = u.id
        WHERE h.order_id = ?
        ORDER BY h.changed_at ASC
    ";
  $stmtHistory = $pdo->prepare($sqlHistory);
  $stmtHistory->execute([$order_id]);
  $history = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

  // 4. Összeállítjuk a választ
  $order['items'] = $items;
  $order['status_history'] = $history;

  successResponse("Rendelés részletei lekérve", $order);
} catch (PDOException $e) {
  http_response_code(500);
  errorResponse("Hiba a rendelés részleteinek lekérdezésekor: " . $e->getMessage());
}


/* 

### Cél  
A `get_order.php` endpoint feladata, hogy a **bejelentkezett felhasználó vagy eladó egy adott rendelés részleteit lekérje**.  
- Csak bejelentkezett user hívhatja meg.  
- Az `order_id` paraméter kötelező (`GET ?id=123`).  
- Ellenőrzi, hogy a rendeléshez tartozik‑e jogosultság (buyer vagy seller).  
- Lekéri az order alapadatait, a rendelés tételeit (`order_items`), valamint a státusztörténetet (`order_status_history`).  
- Egységes JSON válaszformátumot használ.  

---

### Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Jogosultság ellenőrzés → csak buyer vagy seller fér hozzá a rendeléshez.  
  - Vizsgán jól bemutatható → részletes rendelés adat + tételek + státusztörténet.  










## Magyarázat

1. **Jogosultság ellenőrzés**  
   - Csak a rendelés vevője vagy eladója láthatja a részleteket.  
   - Ha más user próbálja, 404‑et kap.

2. **Order alapadatok**  
   - Buyer és seller neve is benne van.  
   - Aktuális státusz is visszajön.

3. **Order items**  
   - Minden tétel a `listings.title` mezővel együtt.  
   - Az ár a rendeléskori (`price_at_order`).

4. **Status history**  
   - Minden váltás időponttal és a váltást végző user nevével.  
   - Rendezve időrendben.

5. **Válasz JSON**  
   - Egyetlen `data` objektum: benne az order alapadatok, `items` tömb, `status_history` tömb.

---

## Példa válasz

```json
{
  "status": "success",
  "message": "Rendelés részletei lekérve",
  "data": {
    "order_id": 101,
    "buyer_id": 5,
    "buyer_name": "andras",
    "seller_id": 7,
    "seller_name": "brickmaster",
    "total_price": 25000,
    "status": "shipped",
    "ordered_at": "2025-10-17 17:00:00",
    "items": [
      {
        "order_item_id": 1,
        "listing_id": 55,
        "listing_title": "LEGO Star Wars X-Wing",
        "quantity": 2,
        "price_at_order": 12000
      }
    ],
    "status_history": [
      {
        "old_status": null,
        "new_status": "pending",
        "changed_at": "2025-10-17 17:00:00",
        "changed_by": "andras"
      },
      {
        "old_status": "pending",
        "new_status": "paid",
        "changed_at": "2025-10-17 17:05:00",
        "changed_by": "andras"
      },
      {
        "old_status": "paid",
        "new_status": "shipped",
        "changed_at": "2025-10-18 09:00:00",
        "changed_by": "brickmaster"
      }
    ]
  }
}
```

*/