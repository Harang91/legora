<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

// Token beolvasása GET paraméterből
$token = $_GET['token'] ?? '';

if ($token === '') {
    http_response_code(400);
    errorResponse("Hiányzó token");
}

try {
    // Token ellenőrzése: csak inaktív fiókhoz tartozhat
    $stmt = $pdo->prepare("SELECT id FROM users WHERE verify_token = ? AND is_active = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(400);
        errorResponse("Érvénytelen vagy már aktivált token");
    }

    // Aktiválás: fiók aktívvá tétele, token törlése
    $stmt = $pdo->prepare("UPDATE users SET is_active = 1, verify_token = NULL WHERE id = ?");
    $stmt->execute([$user['id']]);

    // Sikeres válasz
    successResponse("Fiók sikeresen aktiválva", null);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 
Bevezető leírás
Az `auth/verify.php` végpont felelős a regisztrációkor generált aktiváló token ellenőrzéséért és a fiók aktiválásáért.  
- Csak **GET** kérést fogad, a token paraméterben érkezik.  
- Ha a token érvényes és a fiók inaktív, akkor aktiválja a fiókot.  
- Ha a token hiányzik, érvénytelen vagy már aktivált, hibát ad vissza.  
- Biztonság: a token egyszer használható, aktiválás után törlődik.  
- A regisztrációs folyamat így teljes:  
  1. `register.php` → új user inaktív státusszal, token generálás  
  2. `verify.php` → token ellenőrzés, fiók aktiválás  
  3. `login.php` → csak aktív fiókkal lehet belépni  

  Összegzés
- **Mi változott?**
  - A `header()` és `require_once db.php` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés és válaszformátum → frontend mindig ugyanazt kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → modulonként tiszta, egységes struktúra.  

*/