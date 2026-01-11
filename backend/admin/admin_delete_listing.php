<?php

require_once __DIR__ . '/../shared/init.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}


if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}


$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
if (!$id) {
    http_response_code(422);
    errorResponse("Hiányzik a hirdetés azonosító (id).");
}

try {
   
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

 
    if ($listing['deleted_at'] !== null) {
        http_response_code(409);
        errorResponse("A hirdetés már törölve van.");
    }

    
    $stmtDelete = $pdo->prepare("
        UPDATE listings 
        SET deleted_at = NOW() 
        WHERE id = :id
    ");
    $stmtDelete->execute(['id' => $id]);

    successResponse("Hirdetés sikeresen törölve (soft delete).", [
        "listing_id" => (int)$id,
        "title" => $listing['title']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

