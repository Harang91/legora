<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_user.php
 * ----------------
 * Ez a végpont a bejelentkezett felhasználó adatait adja vissza.
 * - Csak bejelentkezett user hívhatja meg (session ellenőrzés).
 * - Nem ad vissza érzékeny adatokat (pl. jelszó hash).
 */

// Csak GET kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak GET engedélyezett)");
}

// Ellenőrizzük, hogy van‑e bejelentkezett user
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

try {
    // Felhasználó adatainak lekérése
    $stmt = $pdo->prepare("
        SELECT id, username, email, created_at 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        errorResponse("Felhasználó nem található");
    }

    // Sikeres válasz
    successResponse("Felhasználói adatok betöltve", $user);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/*

Bevezető leírás
A `users/get_user.php` végpont a bejelentkezett felhasználó adatait adja vissza.  
- Csak **GET** kérést fogad.  
- Ellenőrzi, hogy a felhasználó be van‑e jelentkezve (`$_SESSION['user_id']`).  
- Lekéri az adott user adatait az adatbázisból (id, username, email, created_at).  
- Nem ad vissza érzékeny adatokat (pl. jelszó hash).  
- JSON formátumban adja vissza az adatokat.

##  Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés és válaszformátum → frontend mindig ugyanazt kapja.  
  - Könnyebb karbantartás → ha változik a session vagy a válasz formátum, csak az `init.php`‑t kell módosítani.  
  - Jól bemutatható → modulonként tiszta, egységes struktúra.

 */