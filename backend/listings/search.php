<?php
// backend/listings/search.php

// 1. Inicializálás (DB kapcsolat, segédfüggvények)
// Ellenőrizzük, hogy a shared/init.php létezik-e, ha nem, manuálisan húzzuk be a db-t
if (file_exists(__DIR__ . '/../shared/init.php')) {
    require_once __DIR__ . '/../shared/init.php';
} else {
    // Fallback, ha nincs init.php
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../shared/lego_helpers.php';
    require_once __DIR__ . '/../shared/response.php'; 
}

// 2. Paraméterek fogadása
$q          = $_GET['q']          ?? null;
$item_type  = $_GET['item_type']  ?? null;
$condition  = $_GET['item_condition'] ?? null; // Figyelem: frontend 'item_condition'-t küldhet
$limit      = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset     = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$mode       = $_GET['mode']       ?? "search";

// 3. SQL összeállítása
// JAVÍTVA: Hozzáadtuk a 'custom_image_url'-t a lekérdezéshez!
$sql = "SELECT 
            l.id AS listing_id, 
            l.item_type, 
            l.item_id, 
            l.item_condition, 
            l.price, 
            l.description, 
            l.created_at, 
            l.custom_image_url, 
            u.username AS seller_name,
            s.name AS set_name,
            m.name AS minifig_name,
            p.name AS part_name
        FROM listings l
        JOIN users u ON l.user_id = u.id
        LEFT JOIN sets s ON (l.item_type = 'set' AND s.set_num = l.item_id)
        LEFT JOIN minifigs m ON (l.item_type = 'minifig' AND m.fig_num = l.item_id)
        LEFT JOIN parts p ON (l.item_type = 'part' AND p.part_num = l.item_id)
        WHERE l.deleted_at IS NULL";


$params = [];

// Dinamikus szűrések
if ($item_type) {
    $sql .= " AND l.item_type = ?";
    $params[] = $item_type;
}

if ($condition) {
    $sql .= " AND l.item_condition = ?";
    $params[] = $condition;
}

// Keresőszó (q)
if ($q) {
    $sql .= " AND (
        l.description LIKE ?
        OR l.item_id LIKE ?
        OR s.name LIKE ?
        OR m.name LIKE ?
        OR p.name LIKE ?
        OR l.item_name LIKE ?
    )";

    $params[] = "%$q%"; // description
    $params[] = "%$q%"; // item_id
    $params[] = "%$q%"; // set name
    $params[] = "%$q%"; // minifig name
    $params[] = "%$q%"; // part name
    $params[] = "%$q%"; // item_name
}


// 4. Rendezés
$sql .= " ORDER BY l.created_at DESC";

// 5. LIMIT és OFFSET (A kritikus javítás!)
// JAVÍTVA: Nem használunk ?-et a limitnél, mert a MariaDB stringként kezelné ('20').
// Helyette közvetlenül beírjuk a számot (int castolás után biztonságos).
if ($mode === "autocomplete") {
    $sql .= " LIMIT 10";
} else {
    $safeLimit = (int)$limit;
    $safeOffset = (int)$offset;
    $sql .= " LIMIT $safeLimit OFFSET $safeOffset";
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Képek és metaadatok feldolgozása
    foreach ($results as &$row) {
        // LEGO adat lekérése (ha van lego_helpers.php)
        if (function_exists('getLegoData')) {
            $legoData = getLegoData($pdo, $row["item_type"], $row["item_id"]);
            
            // JAVÍTVA: Ha van feltöltött saját kép, azt használjuk
            if (!empty($row['custom_image_url'])) {
                // A backend URL-t igazítsd a sajátodhoz!
                $baseUrl = "http://localhost:5173/";
                $legoData['img_url'] = $baseUrl . $row['custom_image_url'];
}

            
            $row["lego_meta"] = $legoData;
        } else {
            // Fallback, ha nincs helper
            $row["lego_meta"] = [
                "name" => $row["description"],
                "img_url" => null
            ];
        }
    }

    // 7. Válasz küldése JSON-ben
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "success",
        "message" => "Találatok",
        "data" => [
            "results" => $results, // A frontend ezt a kulcsot várja!
            "limit" => $limit,
            "offset" => $offset
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Adatbázis hiba: " . $e->getMessage()
    ]);
}
?>