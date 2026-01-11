<?php

require_once __DIR__ . '/../shared/init.php';

// Csak POST kérés engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak POST engedélyezett)");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$buyer_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // Kosár lekérése
    $stmt = $pdo->prepare("
        SELECT c.id AS cart_id, c.quantity AS cart_quantity,
               l.id AS listing_id, l.user_id AS seller_id, l.price, l.quantity AS stock_quantity
        FROM cart c
        JOIN listings l ON c.listing_id = l.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$buyer_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$cartItems) {
        http_response_code(400);
        errorResponse("A kosár üres");
    }

    // Eladók szerinti csoportosítás
    $ordersBySeller = [];
    foreach ($cartItems as $item) {
        $ordersBySeller[$item['seller_id']][] = $item;
    }

    $createdOrders = [];

    foreach ($ordersBySeller as $seller_id => $items) {
        $totalPrice = 0;

        // Összeg és készlet ellenőrzés
        foreach ($items as $item) {
            if ($item['cart_quantity'] > $item['stock_quantity']) {
                throw new Exception("Nincs elegendő készlet a listing_id={$item['listing_id']} termékhez");
            }
            $totalPrice += $item['price'] * $item['cart_quantity'];
        }

        // Order létrehozása
        $insOrder = $pdo->prepare("
            INSERT INTO orders (buyer_id, seller_id, total_price, status, ordered_at)
            VALUES (?, ?, ?, 'pending', NOW())
        ");
        $insOrder->execute([$buyer_id, $seller_id, $totalPrice]);
        $orderId = $pdo->lastInsertId();

        // Státusz naplózása
        $insHistory = $pdo->prepare("
            INSERT INTO order_status_history (order_id, old_status, new_status, changed_by)
            VALUES (?, NULL, 'pending', ?)
        ");
        $insHistory->execute([$orderId, $buyer_id]);

        // Order items létrehozása + készlet frissítése
        $insItem = $pdo->prepare("
            INSERT INTO order_items (order_id, listing_id, quantity, price_at_order)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($items as $item) {
            $insItem->execute([$orderId, $item['listing_id'], $item['cart_quantity'], $item['price']]);

            $updStock = $pdo->prepare("UPDATE listings SET quantity = quantity - ? WHERE id = ?");
            $updStock->execute([$item['cart_quantity'], $item['listing_id']]);
        }

        $createdOrders[] = [
            "order_id" => (int)$orderId,
            "seller_id" => (int)$seller_id,
            "total_price" => $totalPrice,
            "status" => "pending"
        ];
    }

    // Kosár ürítése
    $delCart = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $delCart->execute([$buyer_id]);

    $pdo->commit();

    successResponse("Rendelés(ek) sikeresen létrehozva", $createdOrders);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    errorResponse("Hiba a rendelés létrehozásakor: " . $e->getMessage());
}
