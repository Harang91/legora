<?php

require_once __DIR__ . '/../shared/init.php';
require_once __DIR__ . '/../shared/lego_helpers.php';

// Csak GET kérés engedélyezett
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
    // Kosár tételek lekérése
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
        // LEGO metaadatok
        $item['lego_data'] = getLegoData($pdo, $item['item_type'], $item['item_id']);

        // Sorösszeg
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
