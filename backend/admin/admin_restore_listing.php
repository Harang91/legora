<?php

require_once __DIR__ . '/../shared/init.php';

// Csak POST metódus engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// Admin session ellenőrzése
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

// Kötelező paraméter ellenőrzése
if (!$id) {
    http_response_code(422);
    errorResponse("Hiányzik a hirdetés azonosító (id).");
}

try {
    // Hirdetés lekérése
    $stmt = $pdo->prepare("
        SELECT id, title, deleted_at
        FROM listings
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("Nem található hirdetés ezzel az ID-val.");
    }

    // Ha nincs törölve, nem állítható vissza
    if ($listing['deleted_at'] === null) {
        http_response_code(409);
        errorResponse("A hirdetés nincs törölve, így nem állítható vissza.");
    }

    // Soft delete visszavonása
    $stmtRestore = $pdo->prepare("
        UPDATE listings
        SET deleted_at = NULL
        WHERE id = :id
    ");
    $stmtRestore->execute(['id' => $id]);

    successResponse("Hirdetés sikeresen visszaállítva.", [
        "listing_id" => (int)$id,
        "title" => $listing['title']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
