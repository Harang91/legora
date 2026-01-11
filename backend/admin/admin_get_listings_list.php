<?php

require_once __DIR__ . '/../shared/init.php';


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Csak GET metódus engedélyezett."]);
    exit;
}


if (empty($_SESSION['user_id']) && empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error", 
        "message" => "Nincs bejelentkezve (Session üres).",
        "debug_session" => $_SESSION 
    ]);
    exit;
}

try {

    $sql = "SELECT 
                l.id,
                l.user_id,
                l.item_type,
                l.item_id,               
                l.price,
                l.description,
                l.deleted_at,
                l.created_at,
                u.username AS seller_name
            FROM listings l
            LEFT JOIN users u ON l.user_id = u.id
            ORDER BY l.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "message" => "Hirdetések sikeresen lekérve",
        "count" => count($listings), 
        "listings" => $listings ?: []
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Adatbázis hiba: " . $e->getMessage()]);
}