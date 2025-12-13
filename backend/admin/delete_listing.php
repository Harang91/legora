<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * delete_listing.php (admin modul)
 * -------------------------
 * Admin funkció: hirdetés törlése (soft delete).
 * - Csak POST metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Ellenőrzi, hogy a hirdetés létezik-e.
 * - Ha már törölve van, hibát ad vissza.
 * - Soft delete módon törli a hirdetést (deleted_at = NOW()).
 * - JSON választ ad vissza: success vagy error.
 */

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// Ellenőrizzük, hogy van-e aktív admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}

// Paraméter ellenőrzése
$listingId = $_POST['id'] ?? null;
if (!$listingId) {
    http_response_code(422);
    errorResponse("Hiányzik a hirdetés azonosító (id).");
}

try {
    // Ellenőrizzük, hogy létezik-e a hirdetés
    $stmt = $pdo->prepare("
        SELECT id, title, deleted_at 
        FROM listings 
        WHERE id = :id 
        LIMIT 1
    ");
    $stmt->execute(['id' => $listingId]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("Nem található hirdetés ezzel az ID-val.");
    }

    // Ha már törölve van
    if ($listing['deleted_at'] !== null) {
        http_response_code(409);
        errorResponse("A hirdetés már törölve van.");
    }

    // Soft delete: deleted_at mező beállítása az aktuális időre
    $stmtDelete = $pdo->prepare("
        UPDATE listings 
        SET deleted_at = NOW() 
        WHERE id = :id
    ");
    $stmtDelete->execute(['id' => $listingId]);

    successResponse("Hirdetés sikeresen törölve (soft delete).", [
        "listing_id" => (int)$listingId,
        "title" => $listing['title']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/*

## Cél:  
A `delete_listing.php` az **admin modul** része, amelynek feladata, hogy az adminisztrátor soft delete művelettel törölhessen bármely hirdetést a rendszerből.  

- **Csak POST metódus engedélyezett**, mivel módosító műveletről van szó.  
- **Admin session ellenőrzés** biztosítja, hogy csak bejelentkezett admin férhessen hozzá.  
- **Hirdetés ellenőrzése**: megnézi, hogy létezik‑e a hirdetés az adott ID alapján.  
- **Törlés állapot vizsgálata**: ha a hirdetés már törölve van (`deleted_at` nem null), hibát ad vissza.  
- **Soft delete megvalósítása**: a `deleted_at` mezőt az aktuális időre állítja, így a hirdetés inaktiválódik, de az adatai megmaradnak az adatbázisban.  
- **JSON válasz**: minden esetben egységes választ ad vissza (`success` vagy `error` státusz, részletes üzenettel).  

Ez az endpoint tehát az admin számára biztosítja a hirdetések biztonságos és visszaállítható törlését, összhangban a modul többi funkciójával (`admin_restore_listing.php`, `get_deleted_listings.php`).



Régi:
-beállítja a `deleted_at` mezőt egy adott hirdetésnél. Így lesz mit listázni a `get_deleted_listings.php` modulban.
-- Az admin modulban nem törlöm fizikailag a hirdetést, hanem csak a `deleted_at` mezőt állítom be. Így az adat megmarad, visszakereshető, és a `get_deleted_listings.php` listázza a törölt hirdetéseket.
*/