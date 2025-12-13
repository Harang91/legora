<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * admin_restore_listing.php
 * --------------------------
 * Admin funkció: soft delete-elt hirdetés visszaállítása.
 * - Csak POST metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Ellenőrzi, hogy létezik-e a hirdetés.
 * - Ha nincs törölve, hibát ad vissza.
 * - Soft delete-elt hirdetést visszaállítja (deleted_at = NULL).
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
$id = $_POST['id'] ?? null;
if (!$id) {
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
    $stmt->execute(['id' => $id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        http_response_code(404);
        errorResponse("Nem található hirdetés ezzel az ID-val.");
    }

    // Ha nincs törölve
    if ($listing['deleted_at'] === null) {
        http_response_code(409);
        errorResponse("A hirdetés nincs törölve, így nem állítható vissza.");
    }

    // Restore: deleted_at mező NULL-ra állítása
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

/* 
### Cél  
Az `admin_restore_listing.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy soft delete‑elt hirdetés visszaállítását**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a hirdetés.  
- Ha nincs törölve, hibát ad vissza.  
- Soft delete‑elt hirdetést visszaállítja (`deleted_at = NULL`).  
- JSON választ ad vissza: `success` vagy `error`.  

---
### 📝 Összegzés
- **Mi változott?**
  - Javítva a hibás változó (`$SERVER` → `$_SERVER`).  
  - Beépítve az admin session ellenőrzés → csak bejelentkezett admin férhet hozzá.  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`401`, `404`, `405`, `409`, `422`, `500`).  

- **Miért jobb így?**
  - Biztonságos → csak admin jogosultsággal érhető el.  
  - Megakadályozza a felesleges visszaállítást (ha nincs törölve).  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok, soft delete visszaállítás.  

---




RÉGI:
Összefoglalás
Ez az admin_restore_listing.php script az admin modulban a hirdetés visszaállítását kezeli:  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy létezik-e a hirdetés.  
- Ha nincs törölve, hibát ad vissza.  
- Soft delete‑elt hirdetést visszaállítja (deleted_at = NULL).  
- JSON választ ad vissza: success vagy error.  

Ez jól demonstrálja a teljes körű hirdetéskezelést: törlés és visszaállítás.
*/