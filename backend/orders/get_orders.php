<?php

require_once __DIR__ . '/../shared/init.php';

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$buyer_id = $_SESSION['user_id'];

try {
    // A bejelentkezett felhasználó rendelései
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

    successResponse("Rendelések listázva", $orders);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Hiba a rendelés(ek) lekérdezésekor: " . $e->getMessage());
}
