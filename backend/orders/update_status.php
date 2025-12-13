<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * update_status.php
 * ----------------
 * Egy rendelés státuszának frissítése.
 * - Csak bejelentkezett user hívhatja meg.
 * - Kötelező paraméterek: order_id, new_status (JSON body).
 * - Jogosultság ellenőrzés: csak buyer/seller, meghatározott státuszváltások.
 * - Frissítés az orders táblában + naplózás az order_status_history táblába.
 * - Tranzakcióban fut, rollback hiba esetén.
 */

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$user_id = $_SESSION['user_id'];

// JSON body beolvasása
$input = json_decode(file_get_contents("php://input"), true);

// Kötelező paraméterek ellenőrzése
if (!isset($input['order_id']) || !isset($input['new_status'])) {
    http_response_code(400);
    errorResponse("Hiányzó order_id vagy new_status paraméter");
}

$order_id = (int)$input['order_id'];
$new_status = $input['new_status'];

try {
    $pdo->beginTransaction();

    // 1. Lekérdezzük az aktuális rendelést (lockolva)
    $sqlOrder = "
        SELECT id, buyer_id, seller_id, status
        FROM orders
        WHERE id = ?
        FOR UPDATE
    ";
    $stmt = $pdo->prepare($sqlOrder);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $pdo->rollBack();
        http_response_code(404);
        errorResponse("Nincs ilyen rendelés");
    }

    $old_status = $order['status'];

    // 2. Jogosultság ellenőrzés
    $allowed = false;
    if ($old_status === "pending" && $new_status === "paid" && $order['buyer_id'] == $user_id) {
        $allowed = true;
    } elseif ($old_status === "paid" && $new_status === "shipped" && $order['seller_id'] == $user_id) {
        $allowed = true;
    } elseif ($old_status === "shipped" && $new_status === "completed" && $order['buyer_id'] == $user_id) {
        $allowed = true;
    } elseif ($old_status === "pending" && $new_status === "cancelled" && ($order['buyer_id'] == $user_id || $order['seller_id'] == $user_id)) {
        $allowed = true;
    }

    if (!$allowed) {
        $pdo->rollBack();
        http_response_code(403);
        errorResponse("Nincs jogosultság a státuszváltáshoz vagy érvénytelen váltás");
    }

    // 3. Frissítjük az orders táblát
    $sqlUpdate = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->execute([$new_status, $order_id]);

    // 4. Naplózzuk a váltást az order_status_history táblába
    $sqlHistory = "
        INSERT INTO order_status_history (order_id, old_status, new_status, changed_by)
        VALUES (?, ?, ?, ?)
    ";
    $stmt = $pdo->prepare($sqlHistory);
    $stmt->execute([$order_id, $old_status, $new_status, $user_id]);

    $pdo->commit();

    successResponse("Státusz sikeresen frissítve", [
        "order_id" => $order_id,
        "old_status" => $old_status,
        "new_status" => $new_status
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    errorResponse("Hiba a státusz frissítésekor: " . $e->getMessage());
}
/* 
### Cél  
Az `update_status.php` endpoint feladata, hogy a **bejelentkezett felhasználó vagy eladó frissítse egy rendelés státuszát**.  
- Csak bejelentkezett user hívhatja meg.  
- Kötelező paraméterek: `order_id`, `new_status` (JSON body).  
- Jogosultság ellenőrzés: csak bizonyos státuszváltások engedélyezettek, és csak a megfelelő szereplő (buyer vagy seller) hajthatja végre.  
- Az `orders` táblában frissíti a státuszt.  
- Az `order_status_history` táblába naplózza a változást.  
- Tranzakcióban fut, rollback hiba esetén.  

---

###  Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Jogosultság ellenőrzés → csak a megfelelő szereplő tudja módosítani a státuszt.  
  - Vizsgán jól bemutatható → státuszváltások folyamata, naplózás, tranzakciókezelés.  









régi kódhoz
## Magyarázat

1. **Input**: JSON body (`order_id`, `new_status`).  
2. **Lock**: `FOR UPDATE` zárral lekérdezzük az aktuális rendelést, hogy tranzakcióban biztonságosan frissítsük.  
3. **Jogosultság ellenőrzés**: csak a buyer vagy seller válthat, a státuszfolyamatnak megfelelően.  
   - `pending → paid` → buyer  
   - `paid → shipped` → seller  
   - `shipped → completed` → buyer  
   - `pending → cancelled` → buyer vagy seller  
4. **Frissítés**: `orders.status` mező frissül.  
5. **Naplózás**: új sor az `order_status_history` táblába.  
6. **Tranzakció**: ha bármi hiba van, rollback.  

---

## Példa request

```http
POST /orders/update_status.php
Content-Type: application/json
Cookie: PHPSESSID=<valid_session>

{
  "order_id": 101,
  "new_status": "paid"
}
```

## Példa response

```json
{
  "status": "success",
  "message": "Státusz sikeresen frissítve",
  "data": {
    "order_id": 101,
    "old_status": "pending",
    "new_status": "paid"
  }
}
*/