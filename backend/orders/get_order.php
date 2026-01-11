<?php

require_once __DIR__ . '/../shared/init.php';

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  errorResponse("Bejelentkezés szükséges");
}

$user_id = $_SESSION['user_id'];

// Kötelező paraméter
if (!isset($_GET['id'])) {
  http_response_code(400);
  errorResponse("Hiányzó order_id paraméter");
}

$order_id = (int)$_GET['id'];

try {
  // Rendelés alapadatok lekérése (jogosultság ellenőrzéssel)
  $sqlOrder = "
        SELECT 
            o.id AS order_id,
            o.buyer_id,
            buyer.username AS buyer_name,
            o.seller_id,
            seller.username AS seller_name,
            o.total_price,
            o.status,
            o.ordered_at
        FROM orders o
        JOIN users buyer ON o.buyer_id = buyer.id
        JOIN users seller ON o.seller_id = seller.id
        WHERE o.id = ?
          AND (o.buyer_id = ? OR o.seller_id = ?)
    ";
  $stmt = $pdo->prepare($sqlOrder);
  $stmt->execute([$order_id, $user_id, $user_id]);
  $order = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$order) {
    http_response_code(404);
    errorResponse("Nincs ilyen rendelés, vagy nincs jogosultságod megtekinteni");
  }

  // Rendelés tételei
  $sqlItems = "
        SELECT 
            oi.id AS order_item_id,
            oi.listing_id,
            l.title AS listing_title,
            oi.quantity,
            oi.price_at_order
        FROM order_items oi
        JOIN listings l ON oi.listing_id = l.id
        WHERE oi.order_id = ?
    ";
  $stmtItems = $pdo->prepare($sqlItems);
  $stmtItems->execute([$order_id]);
  $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

  // Státusztörténet
  $sqlHistory = "
        SELECT 
            h.old_status,
            h.new_status,
            h.changed_at,
            u.username AS changed_by
        FROM order_status_history h
        JOIN users u ON h.changed_by = u.id
        WHERE h.order_id = ?
        ORDER BY h.changed_at ASC
    ";
  $stmtHistory = $pdo->prepare($sqlHistory);
  $stmtHistory->execute([$order_id]);
  $history = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

  // Válasz összeállítása
  $order['items'] = $items;
  $order['status_history'] = $history;

  successResponse("Rendelés részletei lekérve", $order);
} catch (PDOException $e) {
  http_response_code(500);
  errorResponse("Hiba a rendelés részleteinek lekérdezésekor: " . $e->getMessage());
}
