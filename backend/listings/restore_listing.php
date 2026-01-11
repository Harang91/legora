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
    errorResponse("Bejelentkezés szükséges a hirdetés visszaállításához");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$listing_id = (int)($input['listing_id'] ?? 0);

if ($listing_id <= 0) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó listing_id");
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

    // Jogosultság ellenőrzése (saját hirdetés vagy admin)
    if ($listing['user_id'] != $_SESSION['user_id'] && ($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        errorResponse("Nincs jogosultságod ennek a hirdetésnek a visszaállítására");
    }

    // Csak törölt hirdetés állítható vissza
    if ($listing['deleted_at'] === null) {
        http_response_code(409);
        errorResponse("A hirdetés nincs törölve");
    }

    // Visszaállítás
    $upd = $pdo->prepare("UPDATE listings SET deleted_at = NULL WHERE id = ?");
    $upd->execute([$listing_id]);

    successResponse("Hirdetés sikeresen visszaállítva", [
        "listing_id" => $listing_id
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}
