<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * add_rating.php
 * ----------------
 * Új értékelés hozzáadása egy eladóhoz.
 * - Csak bejelentkezett user hívhatja meg.
 * - Feltétel: completed rendelés a két fél között.
 * - Body: JSON { rated_user_id, rating, comment }
 * - Ha már van értékelés → frissítjük.
 * - Egységes JSON válasz formátum.
 */

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak POST engedélyezett)");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

$rater_id = $_SESSION['user_id'];

// Request body beolvasása
$input = json_decode(file_get_contents('php://input'), true);

$rated_user_id = $input['rated_user_id'] ?? null;
$rating        = isset($input['rating']) ? (int)$input['rating'] : null;
$comment       = $input['comment'] ?? null;

// Alap validáció
if (!$rated_user_id || !$rating || $rating < 1 || $rating > 5) {
    http_response_code(422);
    errorResponse("Érvénytelen vagy hiányzó mezők (rated_user_id, rating 1-5 között kötelező)");
}

// Önvédelem: saját magát nem értékelheti senki
if ($rater_id === (int)$rated_user_id) {
    http_response_code(403);
    errorResponse("Saját magadat nem értékelheted");
}

try {
    // Ellenőrizzük, hogy volt-e completed rendelés a két fél között
    $checkOrder = $pdo->prepare("
        SELECT COUNT(*) 
        FROM orders 
        WHERE buyer_id = ? 
          AND seller_id = ? 
          AND status = 'completed'
    ");
    $checkOrder->execute([$rater_id, $rated_user_id]);
    $hasOrder = $checkOrder->fetchColumn();

    if (!$hasOrder) {
        http_response_code(403);
        errorResponse("Csak akkor értékelhetsz, ha már vásároltál ettől az eladótól (completed rendelés szükséges)");
    }

    // Ellenőrizzük, hogy van-e már értékelés
    $checkRating = $pdo->prepare("SELECT id FROM ratings WHERE rater_id = ? AND rated_user_id = ?");
    $checkRating->execute([$rater_id, $rated_user_id]);
    $existing = $checkRating->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Frissítjük a meglévő értékelést
        $upd = $pdo->prepare("UPDATE ratings SET rating = ?, comment = ?, rated_at = NOW() WHERE id = ?");
        $upd->execute([$rating, $comment, $existing['id']]);

        successResponse("Értékelés frissítve", [
            "rating_id" => (int)$existing['id'],
            "rating" => $rating,
            "comment" => $comment
        ]);
    } else {
        // Új értékelés beszúrása
        $ins = $pdo->prepare("INSERT INTO ratings (rater_id, rated_user_id, rating, comment, rated_at)
                              VALUES (?, ?, ?, ?, NOW())");
        $ins->execute([$rater_id, $rated_user_id, $rating, $comment]);

        successResponse("Értékelés sikeresen hozzáadva", [
            "rating_id" => (int)$pdo->lastInsertId(),
            "rating" => $rating,
            "comment" => $comment
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 
### Cél  
Az `add_rating.php` endpoint feladata, hogy a **felhasználó új értékelést adjon egy eladóhoz**, vagy frissítse a meglévőt.  
- Csak bejelentkezett user hívhatja meg.  
- Feltétel: a user vásárolt már az adott eladótól, és van legalább egy `completed` státuszú rendelése.  
- Kötelező paraméterek: `rated_user_id`, `rating` (1–5).  
- Opcionális: `comment`.  
- Ha már létezik értékelés ugyanettől a vásárlótól ugyanarra az eladóra → frissítjük.  
- Egységes JSON válaszformátumot ad vissza.  

---

###  Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Jogosultság ellenőrzés → csak akkor lehet értékelni, ha tényleges vásárlás történt.  
  - Vizsgán jól bemutatható → POST metódus, validáció, jogosultság, duplikáció kezelése.  





régi kód
## Magyarázat

- **Autentikáció**: csak bejelentkezett user hívhatja meg.  
- **Validáció**: kötelező a `rated_user_id` és a `rating` (1–5).  
- **Önvédelem**: saját magát nem értékelheti senki.  
- **Jogosultság ellenőrzés**: csak akkor engedjük az értékelést, ha van legalább egy `completed` státuszú rendelés a két fél között (`orders` táblában).  
- **Duplikáció kezelése**: ha már van értékelés ugyanettől a vásárlótól ugyanarra az eladóra, akkor frissítjük a meglévőt.  
- **Válasz**: visszaadjuk az értékelés azonosítóját, a rating értékét és a kommentet.  

---

*/