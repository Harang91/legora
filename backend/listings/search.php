<?php

// Inicializálás
if (file_exists(__DIR__ . '/../shared/init.php')) {
    require_once __DIR__ . '/../shared/init.php';
} else {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../shared/lego_helpers.php';
    require_once __DIR__ . '/../shared/response.php';
}

// Paraméterek
$q          = $_GET['q'] ?? null;
$item_type  = $_GET['item_type'] ?? null;
$condition  = $_GET['item_condition'] ?? null;
$limit      = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset     = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$mode       = $_GET['mode'] ?? "search";

// Alap SQL (csak aktív hirdetések)
$sql = "SELECT 
            l.id AS listing_id,
            l.item_name,
            l.item_type,
            l.item_id,
            l.item_condition,
            l.price,
            l.quantity,
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

// Szűrések
if ($item_type) {
    $sql .= " AND l.item_type = ?";
    $params[] = $item_type;
}

if ($condition) {
    $sql .= " AND l.item_condition = ?";
    $params[] = $condition;
}

// Keresőszó
if ($q) {
    $sql .= " AND (
        l.description LIKE ?
        OR l.item_id LIKE ?
        OR s.name LIKE ?
        OR m.name LIKE ?
        OR p.name LIKE ?
        OR l.item_name LIKE ?
    )";

    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

// Rendezés
$sql .= " ORDER BY l.created_at DESC";

// LIMIT / OFFSET
if ($mode === "autocomplete") {
    $sql .= " LIMIT 10";
} else {
    $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Metaadatok
    foreach ($results as &$row) {
        if (function_exists('getLegoData')) {
            $legoData = getLegoData($pdo, $row["item_type"], $row["item_id"]);

            if (!empty($row['custom_image_url'])) {
                $legoData['img_url'] = "http://localhost:5173/" . $row['custom_image_url'];
            }

            $row["lego_meta"] = $legoData;
        } else {
            $row["lego_meta"] = [
                "name" => $row["description"],
                "img_url" => null
            ];
        }
    }

    echo json_encode([
        "status" => "success",
        "message" => "Találatok",
        "data" => [
            "results" => $results,
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
