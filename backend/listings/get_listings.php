<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_listings.php
 * ----------------
 * A piactér hirdetéseinek listázása.
 * - Csak GET kérést enged.
 * - Lapozás és szűrés támogatott.
 * - Csak aktív hirdetések (deleted_at IS NULL).
 * - LEGO metaadatok automatikus csatolása.
 */

// Csak GET kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak GET engedélyezett)");
}

// Lapozás paraméterek
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = max(1, min(100, (int)($_GET['limit'] ?? 20))); // max 100 elem
$offset = ($page - 1) * $limit;

// Szűrési paraméterek
$item_type = $_GET['item_type'] ?? null;
$seller_id = $_GET['seller_id'] ?? null;

try {
    // Alap SQL (csak aktív hirdetések)
    // JAVÍTVA: Hozzáadtuk az 'l.custom_image_url' mezőt a felsoroláshoz!
    $sql = "SELECT l.id, l.item_type, l.item_id, l.item_name, l.quantity, l.price, l.item_condition,
                   l.description, l.created_at, l.custom_image_url, u.username AS seller
            FROM listings l
            JOIN users u ON l.user_id = u.id
            WHERE l.deleted_at IS NULL"; // logikai törlés szűrés
    
    $params = [];

    // Szűrés item_type alapján
    if ($item_type) {
        $sql .= " AND l.item_type = ?";
        $params[] = $item_type;
    }

    // Szűrés seller_id alapján
    if ($seller_id) {
        $sql .= " AND l.user_id = ?";
        $params[] = $seller_id;
    }

    // Összes találat száma (lapozáshoz)
    $countSql = "SELECT COUNT(*) FROM ($sql) AS sub";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // LIMIT és OFFSET közvetlen beillesztése (MariaDB nem engedi paraméterként)
    $sql .= " ORDER BY l.created_at DESC LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // LEGO metaadatok hozzácsatolása a helperen keresztül
    
    foreach ($listings as &$listing) { // Alapértelmezett LEGO adatok
         $legoData = getLegoData($pdo, $listing["item_type"], $listing["item_id"]); 
         // Ha van saját kép, felülírjuk 
         if (!empty($listing['custom_image_url'])) { $baseUrl = "http://localhost:5173/";
             $legoData['img_url'] = $baseUrl . $listing['custom_image_url'];
             }
              // Hozzáadjuk a metaadatokat
               $listing['lego_meta'] = $legoData;
             
}

    // Egységes sikeres válasz
    successResponse("Hirdetések listázva", [
        "listings" => $listings,
        "pagination" => [
            "page" => $page,
            "limit" => $limit,
            "total" => $total
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 

Backend összefoglaló – `get_listings.php`

### Cél
A `get_listings.php` endpoint feladata, hogy a piactér hirdetéseit lekérje, és a felhasználói adatok mellett automatikusan csatolja a hivatalos LEGO metaadatokat is.  
Így a frontend azonnal meg tudja jeleníteni a hirdetést a hivatalos képpel, névvel, évvel, színnel – az eladó által megadott hiányosságokkal kiegészítve.
A `get_listings.php` végpont feladata, hogy a piactér hirdetéseit lekérje, és a felhasználói adatok mellett automatikusan csatolja a hivatalos LEGO metaadatokat is.  
- Csak **GET** kérést enged.  
- Lapozás (`page`, `limit`) és szűrés (`item_type`, `seller_id`) támogatott.  
- Csak aktív hirdetések (`deleted_at IS NULL`).  
- LEGO metaadatok (`lego_data`) a helperen keresztül kerülnek be.  
- Egységes JSON válasz formátum.  
---

### Fő funkciók
1. **HTTP metódus ellenőrzés**  
   - Csak `GET` kérést enged.  
   - Más metódus → `405 Method Not Allowed`.

2. **Lapozás (pagination)**  
   - Paraméterek: `page`, `limit`.  
   - Alapértelmezett: `page=1`, `limit=20`.  
   - Biztonság: `limit` max. 100, minden érték integerre castolva.  
   - MariaDB kompatibilitás: `LIMIT` és `OFFSET` közvetlenül kerül az SQL‑be.

3. **Szűrési lehetőségek**  
   - `item_type` → szűrés típus szerint (`set`, `part`, `minifig`).  
   - `seller_id` → szűrés adott felhasználó hirdetéseire.

4. **Kapcsolatok**  
   - `listings.user_id` → `users.id` → `users.username` (alias: `seller`).  
   - `listings.item_type` + `listings.item_id` → statikus LEGO táblák (`sets`, `parts`, `minifigs`).

5. **LEGO metaadatok (`lego_data`)**  
   - `set` → `sets.name`, `sets.year`, `sets.img_url`  
   - `part` → `parts.name`, `colors.name`, `colors.rgb`  
   - `minifig` → `minifigs.name`, `minifigs.img_url`  
   - Ezeket a backend illeszti be a JSON válaszba, **nem adatbázis oszlopok**.

6. **Egységes JSON válasz**  
   - `status` (success/error)  
   - `message` (emberi olvasható üzenet)  
   - `data` → `listings` tömb + `pagination` objektum  
   - Hibák esetén megfelelő HTTP státuszkód (400, 401, 403, 405, 409, 422, 500).
*/
