<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_ratings.php
 * ----------------
 * Egy adott felhasználóhoz tartozó értékelések lekérése.
 * - Paraméter: rated_user_id (kötelező, GET query paraméter).
 * - Visszaadja az összes értékelést, az értékelők felhasználónevével együtt.
 * - Kiszámolja az átlagos értékelést is.
 * - Egységes JSON válasz formátum.
 */

// Csak GET kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak GET engedélyezett)");
}

// Kötelező paraméter ellenőrzése
$rated_user_id = $_GET['rated_user_id'] ?? null;
if (!$rated_user_id) {
    http_response_code(422);
    errorResponse("Hiányzó rated_user_id paraméter");
}

try {
    // Értékelések lekérése JOIN-nal a rater user nevére
    $stmt = $pdo->prepare("
        SELECT 
            r.id AS rating_id,
            r.rating,
            r.comment,
            r.rated_at,
            u.username AS rater_username,
            u.id AS rater_id
        FROM ratings r
        JOIN users u ON r.rater_id = u.id
        WHERE r.rated_user_id = ?
        ORDER BY r.rated_at DESC
    ");
    $stmt->execute([$rated_user_id]);
    $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Átlagos értékelés számítása
    $avg = null;
    if ($ratings) {
        $sum = 0;
        foreach ($ratings as $row) {
            $sum += (int)$row['rating'];
        }
        $avg = round($sum / count($ratings), 2);
    }

    successResponse("Értékelések lekérve", [
        "rated_user_id" => (int)$rated_user_id,
        "average_rating" => $avg,
        "total_ratings" => count($ratings),
        "ratings" => $ratings
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/* 
### Cél  
A `get_ratings.php` endpoint feladata, hogy **egy adott felhasználóhoz tartozó értékeléseket lekérje**.  
- Paraméter: `rated_user_id` (kötelező, GET query paraméter).  
- Visszaadja az összes értékelést, az értékelők felhasználónevével együtt.  
- Kiszámolja az átlagos értékelést is.  
- Egységes JSON válaszformátumot ad vissza.  

###  Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Átlagos értékelés kiszámítása → felhasználóbarát összegzés.  
  - Vizsgán jól bemutatható → GET metódus, paraméter validáció, JOIN, aggregáció (átlag).  



régihez
## Magyarázat

- **Paraméter**: kötelező a `rated_user_id` (GET query paraméter).  
- **JOIN**: a `ratings` táblát összekapcsoljuk a `users` táblával, hogy az értékelő felhasználóneve is megjelenjen.  
- **Átlag**: kiszámoljuk az adott user átlagos értékelését, két tizedesre kerekítve.  
- **Válasz**: tartalmazza a `rated_user_id`‑t, az átlagot, az értékelések számát, és az összes értékelést részletesen.  


*/