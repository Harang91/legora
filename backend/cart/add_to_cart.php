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

$user_id = $_SESSION['user_id'];

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);

$listing_id = $input['listing_id'] ?? null;
$quantity   = isset($input['quantity']) ? (int)$input['quantity'] : null;

// Alap validáció
if (!$listing_id || !$quantity || $quantity < 1) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó mezők");
}

try {
    // Hirdetés ellenőrzése
    $checkListing = $pdo->prepare("SELECT id, deleted_at FROM listings WHERE id = ?");
    $checkListing->execute([$listing_id]);
    $listing = $checkListing->fetch(PDO::FETCH_ASSOC);

    if (!$listing || $listing['deleted_at'] !== null) {
        http_response_code(404);
        errorResponse("A hirdetés nem található vagy törölve lett");
    }

    // Meglévő kosár tétel ellenőrzése
    $checkCart = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND listing_id = ?");
    $checkCart->execute([$user_id, $listing_id]);
    $existing = $checkCart->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Mennyiség frissítése
        $newQty = (int)$existing['quantity'] + $quantity;
        $upd = $pdo->prepare("UPDATE cart SET quantity = ?, added_at = NOW() WHERE id = ?");
        $upd->execute([$newQty, $existing['id']]);

        successResponse("Kosár tétel frissítve", [
            "cart_item_id" => (int)$existing['id'],
            "quantity" => $newQty
        ]);
    } else {
        // Új tétel beszúrása
        $ins = $pdo->prepare("INSERT INTO cart (user_id, listing_id, quantity, added_at)
                              VALUES (?, ?, ?, NOW())");
        $ins->execute([$user_id, $listing_id, $quantity]);

        successResponse("Tétel hozzáadva a kosárhoz", [
            "cart_item_id" => (int)$pdo->lastInsertId(),
            "quantity" => $quantity
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
