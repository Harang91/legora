<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
// Feltételezzük, hogy a 'getLegoData' függvény a helpers-ben vagy az init-ben van definiálva.
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

// 1. Csak GET kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak GET engedélyezett)");
    exit;
}

// 2. Lapozás paraméterek
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = max(1, min(100, (int)($_GET['limit'] ?? 20))); // max 100 elem
$offset = ($page - 1) * $limit;

// 3. Szűrési paraméterek
$item_type = $_GET['item_type'] ?? null;
$seller_id = $_GET['seller_id'] ?? null;

try {
    // 4. SQL LEKÉRDEZÉS ÖSSZEÁLLÍTÁSA
    // Fontos: Itt kérjük le a 'custom_image_url'-t is!
    $sql = "SELECT l.id, 
                   l.id as listing_id, 
                   l.item_type, 
                   l.item_id, 
                   l.quantity, 
                   l.price, 
                   l.item_condition,
                   l.description, 
                   l.created_at, 
                   l.custom_image_url, 
                   u.username AS seller,
                   u.username AS seller_name
            FROM listings l
            JOIN users u ON l.user_id = u.id
            WHERE l.deleted_at IS NULL"; // Csak az aktív hirdetések
    
    $params = [];

    // Szűrés item_type alapján (pl. set, part, minifig)
    if ($item_type) {
        $sql .= " AND l.item_type = ?";
        $params[] = $item_type;
    }

    // Szűrés seller_id alapján (ha egy eladó cuccait nézzük)
    if ($seller_id) {
        $sql .= " AND l.user_id = ?";
        $params[] = $seller_id;
    }

    // 5. Összes találat száma (a lapozáshoz kell)
    $countSql = "SELECT COUNT(*) FROM listings l WHERE l.deleted_at IS NULL";
    // A count SQL-nek is tartalmaznia kell a WHERE feltételeket, ha szűrünk
    if ($item_type) $countSql .= " AND l.item_type = '$item_type'"; // Egyszerűsített beillesztés count-hoz
    if ($seller_id) $countSql .= " AND l.user_id = '$seller_id'";

    $countStmt = $pdo->prepare($countSql);
    // Itt egyszerűsített paraméterezést használunk vagy újraépíthetjük a params tömböt, 
    // de a fenti $params tömböt használva a "SELECT COUNT(*) FROM ... JOIN ..." lenne a legtisztább.
    // A biztos megoldás érdekében használjuk az eredeti $params-t és az eredeti WHERE részt:
    $countSqlFull = "SELECT COUNT(*) FROM listings l JOIN users u ON l.user_id = u.id WHERE l.deleted_at IS NULL";
    if ($item_type) $countSqlFull .= " AND l.item_type = ?";
    if ($seller_id) $countSqlFull .= " AND l.user_id = ?";
    
    $countStmt = $pdo->prepare($countSqlFull);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // 6. VÉGLEGES LEKÉRDEZÉS FUTTATÁSA (Rendezés + Limit)
    $sql .= " ORDER BY l.created_at DESC LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 7. ADATOK FELDOLGOZÁSA (LEGO Data + Képek)
    // Ez a rész felel azért, hogy a React kód megkapja a képeket
    
    // FONTOS: Ezt állítsd be a saját szervered elérési útjára!
    // A végén legyen ott a perjel (/) és az 'uploads/' mappa!
    $baseUrl = "http://localhost/legora_final2/backend/uploads/";

    foreach ($listings as &$listing) {
        // A. Hivatalos LEGO adatok lekérése (név, gyári kép)
        // Feltételezzük, hogy ez a függvény létezik az init.php-ban importált helperben
        if (function_exists('getLegoData')) {
            $legoData = getLegoData($pdo, $listing['item_type'], $listing['item_id']);
        } else {
            // Fallback, ha nincs meg a függvény
            $legoData = ['name' => 'Ismeretlen elem', 'img_url' => null];
        }

        // B. Saját feltöltött kép kezelése
        if (!empty($listing['custom_image_url'])) {
            // 1. Összerakjuk a teljes URL-t a frontendnek
            $fullImageUrl = $baseUrl . $listing['custom_image_url'];
            
            // 2. Felülírjuk a LEGO gyári képet a saját képpel (így a kártya ezt mutatja)
            $legoData['img_url'] = $fullImageUrl;

            // 3. A React kompatibilitás miatt a gyökérbe is kitesszük 'image_url' néven
            $listing['image_url'] = $listing['custom_image_url'];
        } else {
            $listing['image_url'] = null;
        }

        // C. Adatok csatolása
        $listing['lego_data'] = $legoData;
    }

    // 8. VÁLASZ KÜLDÉSE
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
?>