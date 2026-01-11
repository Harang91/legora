<?php

require_once __DIR__ . '/../shared/init.php';

// Csak PUT/PATCH kérés engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak PUT/PATCH engedélyezett)");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges a hirdetés módosításához");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$listing_id = (int)($input['listing_id'] ?? 0);

if ($listing_id <= 0) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó listing_id");
}

// Frissíthető mezők
$allowed_fields = ['quantity', 'price', 'item_condition', 'description'];
$updates = [];
$params = [];

foreach ($allowed_fields as $field) {
    if (isset($input[$field])) {
        $updates[] = "$field = ?";
        $params[] = $input[$field];
    }
}

if (empty($updates)) {
    http_response_code(422);
    errorResponse("Nincs frissíthető mező megadva");
}

try {
    // Hirdetés ellenőrzése
    $stmt = $pdo->prepare("SELECT user_id, deleted_at FROM listings WHERE id = ?");
    $stmt->execute([$listing_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("A hirdetés nem található");
    }

    if ($listing['user_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        errorResponse("Nincs jogosultságod ennek a hirdetésnek a módosítására");
    }

    if ($listing['deleted_at'] !== null) {
        http_response_code(409);
        errorResponse("A hirdetés már törölve lett, nem módosítható");
    }

    // UPDATE futtatása
    $sql = "UPDATE listings SET " . implode(", ", $updates) . " WHERE id = ? AND user_id = ?";
    $params[] = $listing_id;
    $params[] = $_SESSION['user_id'];

    $upd = $pdo->prepare($sql);
    $upd->execute($params);

    successResponse("Hirdetés sikeresen frissítve", [
        "listing_id" => $listing_id,
        "updated_fields" => array_keys(array_intersect_key($input, array_flip($allowed_fields)))
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
