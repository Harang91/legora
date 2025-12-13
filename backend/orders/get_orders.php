<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_orders.php
 * ----------------
 * A bejelentkezett felhasználó rendeléseinek listázása.
 * - Csak bejelentkezett user hívhatja meg.
 * - Az orders táblából minden rendelést lekér, ahol buyer_id = aktuális user.
 * - JOIN a users táblával → seller_name megjelenítése.
 * - Rendezés: legfrissebb rendelés elöl.
 * - Egységes JSON válasz formátum.
 */

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$buyer_id = $_SESSION['user_id'];

try {
    // SQL lekérdezés: a bejelentkezett user rendelései
    $sql = "
        SELECT 
            o.id AS order_id,
            o.seller_id,
            o.total_price,
            o.status,
            o.ordered_at,
            u.username AS seller_name
        FROM orders o
        JOIN users u ON o.seller_id = u.id
        WHERE o.buyer_id = ?
        ORDER BY o.ordered_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$buyer_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sikeres válasz
    successResponse("Rendelések listázva", $orders);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Hiba a rendelés(ek) lekérdezésekor: " . $e->getMessage());
}

/* 

### Cél  
A `get_orders.php` endpoint feladata, hogy a **bejelentkezett felhasználó rendeléseit listázza**.  
- Csak bejelentkezett user hívhatja meg.  
- Az `orders` táblából minden olyan rekordot lekér, ahol a `buyer_id` = aktuális user.  
- A `users` táblával JOIN-olva megjeleníti az eladó nevét (`seller_name`).  
- A rendeléseket időrendben adja vissza, legfrissebb elöl.  
- Egységes JSON válaszformátumot használ.  

---

### Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → egyszerű, tiszta lekérdezés, JOIN az eladó nevével, rendezés.  

---


## Magyarázat

- **Bejelentkezés ellenőrzése**: ha nincs `$_SESSION["user_id"]`, akkor 401 hibát adunk vissza.  
- **Lekérdezés**: az `orders` táblából minden olyan rekordot lekérünk, ahol a `buyer_id` = bejelentkezett user.  
- **JOIN**: csatlakoztatjuk a `users` táblát, hogy a `seller_name` is megjelenjen.  
- **Rendezés**: legfrissebb rendelés elöl (`ORDER BY ordered_at DESC`).  
- **Válasz**: JSON formátumban adunk vissza egy tömböt, minden rendelésről az alapadatokkal.

*/