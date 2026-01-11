<?php

require_once __DIR__ . '/../shared/init.php';

// Csak DELETE kérés engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak DELETE engedélyezett)");
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

// Validáció
if (!$listing_id || !$quantity || $quantity < 1) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó mezők");
}

try {
    // Kosár tétel ellenőrzése
    $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND listing_id = ?");
    $check->execute([$user_id, $listing_id]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        http_response_code(404);
        errorResponse("A tétel nem található a kosárban");
    }

    $currentQty = (int)$existing['quantity'];

    if ($currentQty > $quantity) {
        // Mennyiség csökkentése
        $newQty = $currentQty - $quantity;
        $upd = $pdo->prepare("UPDATE cart SET quantity = ?, added_at = NOW() WHERE id = ?");
        $upd->execute([$newQty, $existing['id']]);

        successResponse("Kosár tétel mennyisége csökkentve", [
            "cart_item_id" => (int)$existing['id'],
            "quantity" => $newQty
        ]);
    } else {
        // Teljes törlés
        $del = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $del->execute([$existing['id']]);

        successResponse("Tétel eltávolítva a kosárból", [
            "cart_item_id" => (int)$existing['id']
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
