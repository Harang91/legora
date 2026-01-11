<?php

require_once __DIR__ . '/../shared/init.php';

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$user_id = $_SESSION['user_id'];

// JSON body beolvasása
$input = json_decode(file_get_contents("php://input"), true);

// Kötelező paraméterek
if (!isset($input['order_id']) || !isset($input['new_status'])) {
    http_response_code(400);
    errorResponse("Hiányzó order_id vagy new_status paraméter");
}

$order_id = (int)$input['order_id'];
$new_status = $input['new_status'];

try {
    $pdo->beginTransaction();

    // Rendelés lekérése (lockolva)
    $sqlOrder = "
        SELECT id, buyer_id, seller_id, status
        FROM orders
        WHERE id = ?
        FOR UPDATE
    ";
    $stmt = $pdo->prepare($sqlOrder);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $pdo->rollBack();
        http_response_code(404);
        errorResponse("Nincs ilyen rendelés");
    }

    $old_status = $order['status'];

    // Jogosultság és érvényes státuszváltások
    $allowed = false;
    if ($old_status === "pending" && $new_status === "paid" && $order['buyer_id'] == $user_id) {
        $allowed = true;
    } elseif ($old_status === "paid" && $new_status === "shipped" && $order['seller_id'] == $user_id) {
        $allowed = true;
    } elseif ($old_status === "shipped" && $new_status === "completed" && $order['buyer_id'] == $user_id) {
        $allowed = true;
    } elseif ($old_status === "pending" && $new_status === "cancelled" && ($order['buyer_id'] == $user_id || $order['seller_id'] == $user_id)) {
        $allowed = true;
    }

    if (!$allowed) {
        $pdo->rollBack();
        http_response_code(403);
        errorResponse("Nincs jogosultság a státuszváltáshoz vagy érvénytelen váltás");
    }

    // Státusz frissítése
    $sqlUpdate = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->execute([$new_status, $order_id]);

    // Naplózás
    $sqlHistory = "
        INSERT INTO order_status_history (order_id, old_status, new_status, changed_by)
        VALUES (?, ?, ?, ?)
    ";
    $stmt = $pdo->prepare($sqlHistory);
    $stmt->execute([$order_id, $old_status, $new_status, $user_id]);

    $pdo->commit();

    successResponse("Státusz sikeresen frissítve", [
        "order_id" => $order_id,
        "old_status" => $old_status,
        "new_status" => $new_status
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    errorResponse("Hiba a státusz frissítésekor: " . $e->getMessage());
}
