<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * update_user.php
 * ----------------
 * Ez a végpont a bejelentkezett felhasználó adatait frissíti.
 * - Csak bejelentkezett user hívhatja meg.
 * - Frissíthető mezők: email, username, password, address, phone.
 * - Jelszó esetén bcrypt hash készül.
 */

// Csak PUT/PATCH kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak PUT/PATCH engedélyezett)");
}

// Ellenőrizzük, hogy van‑e bejelentkezett user
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$fields = [];
$params = [];

// Frissíthető mezők ellenőrzése és validáció
if (isset($input['email'])) {
    if (!validateEmail($input['email'])) {
        http_response_code(422);
        errorResponse("Hibás email formátum");
    }
    $fields[] = "email = ?";
    $params[] = $input['email'];
}

if (isset($input['username'])) {
    $fields[] = "username = ?";
    $params[] = $input['username'];
}

if (isset($input['password'])) {
    if (!validatePassword($input['password'])) {
        http_response_code(422);
        errorResponse("Gyenge jelszó (minimum 8 karakter, szám és betű szükséges)");
    }
    $fields[] = "password_hash = ?";
    $params[] = password_hash($input['password'], PASSWORD_BCRYPT);
}

if (isset($input['address'])) {
    if (!validateAddress($input['address'])) {
        http_response_code(422);
        errorResponse("Hibás lakcím formátum");
    }
    $fields[] = "address = ?";
    $params[] = $input['address'];
}

if (isset($input['phone'])) {
    if (!validatePhone($input['phone'])) {
        http_response_code(422);
        errorResponse("Hibás telefonszám formátum");
    }
    $fields[] = "phone = ?";
    $params[] = $input['phone'];
}

if (empty($fields)) {
    http_response_code(422);
    errorResponse("Nincs frissíthető mező megadva");
}

$params[] = $_SESSION['user_id'];

try {
    // Dinamikus SQL összeállítása
    $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Sikeres válasz
    successResponse("Felhasználói adatok frissítve", [
        "updated_fields" => array_keys($input)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 
Bevezető leírás
Az `update_user.php` végpont a bejelentkezett felhasználó adatait frissíti.  
- Csak **PUT/PATCH** kérést fogad.  
- Csak bejelentkezett user hívhatja meg.  
- Frissíthető mezők: `email`, `username`, `password`, `address`, `phone`.  
- Jelszó esetén bcrypt hash készül.  
- Visszaadja, mely mezők frissültek.


 Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés és válaszformátum → frontend mindig ugyanazt kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → modulonként tiszta, egységes struktúra.  

*/