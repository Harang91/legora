<?php

require_once __DIR__ . '/../shared/init.php';

// Csak GET kérés engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak GET engedélyezett)");
}

// Lapozás
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = max(1, min(100, (int)($_GET['limit'] ?? 20)));
$offset = ($page - 1) * $limit;

// Szűrés
$item_type = $_GET['item_type'] ?? null;
$seller_id = $_GET['seller_id'] ?? null;

try {
    // Alap lekérdezés (csak aktív hirdetések)
    $sql = "SELECT l.id, l.item_type, l.item_id, l.item_name, l.quantity, l.price, l.item_condition,
                   l.description, l.created_at, l.custom_image_url, u.username AS seller
            FROM listings l
            JOIN users u ON l.user_id = u.id
            WHERE l.deleted_at IS NULL";

    $params = [];

    if ($item_type) {
        $sql .= " AND l.item_type = ?";
        $params[] = $item_type;
    }

    if ($seller_id) {
        $sql .= " AND l.user_id = ?";
        $params[] = $seller_id;
    }

    // Összes találat száma
    $countSql = "SELECT COUNT(*) FROM ($sql) AS sub";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // Lapozás
    $sql .= " ORDER BY l.created_at DESC LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // LEGO metaadatok csatolása
    foreach ($listings as &$listing) {
        $legoData = getLegoData($pdo, $listing["item_type"], $listing["item_id"]);

        if (!empty($listing['custom_image_url'])) {
            $baseUrl = "http://localhost:5173/";
            $legoData['img_url'] = $baseUrl . $listing['custom_image_url'];
        }

        $listing['lego_meta'] = $legoData;
    }

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
