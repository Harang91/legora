
<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak POST engedélyezett)");
}

// JSON body beolvasása
$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    errorResponse("Érvénytelen JSON formátum");
}

// Bemeneti adatok
$username = trim($input['username'] ?? '');
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$captcha  = $input['captcha'] ?? '';

// Validáció
if ($username === '' || $email === '' || $password === '' || $captcha === '') {
    http_response_code(422);
    errorResponse("Minden mező kitöltése kötelező (username, email, password, captcha)");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    errorResponse("Hibás email formátum");
}

// Tiltólista fake e‑mail címekre
$blockedEmails = ['teszt@teszt.com', 'asd@asd.com', 'example@example.com'];
if (in_array(strtolower($email), $blockedEmails)) {
    http_response_code(422);
    errorResponse("Ez az e‑mail cím nem engedélyezett");
}

// Egyszerű CAPTCHA ellenőrzés
if ($captcha !== '1234') {
    http_response_code(403);
    errorResponse("Hibás CAPTCHA");
}

try {
    // Ellenőrizzük, hogy létezik‑e már a felhasználó
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        http_response_code(409);
        errorResponse("Ez az e‑mail vagy felhasználónév már foglalt");
    }

    // Jelszó hash
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Aktiváló token generálása
    $verifyToken = bin2hex(random_bytes(32));

    // Új felhasználó beszúrása inaktív státusszal
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password_hash, is_active, verify_token) 
        VALUES (?, ?, ?, 0, ?)
    ");
    $stmt->execute([$username, $email, $hashedPassword, $verifyToken]);

    // Aktiváló link összeállítása (fejlesztéshez visszaadjuk, élesben nem!)
    $verifyLink = "http://localhost/legora/auth/verify.php?token=" . urlencode($verifyToken);

    // E-mail küldés (modell szinten – vizsgához elég kommentben jelezni)
    // $subject = "Fiók aktiválása";
    // $message = "Kedves $username,\n\nKérlek kattints az alábbi linkre a fiókod aktiválásához:\n$verifyLink\n\nÜdv,\nA rendszer";
    // mail($email, $subject, $message);

    // Sikeres válasz
    http_response_code(201);
    successResponse("Regisztráció sikeres. Kérlek, ellenőrizd az e‑mail fiókodat az aktiváló linkért.", [
        "user_id" => $pdo->lastInsertId(),
        "username" => $username,
        "email" => $email,
        "verify_link" => $verifyLink // csak fejlesztéshez, élesben nem adjuk vissza!
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/* 
Bevezető leírás
Az `auth/register.php` végpont felelős az új felhasználók regisztrációjáért.  
- Csak **POST** kérést fogad.  
- Validálja a mezőket (username, email, password, captcha).  
- Ellenőrzi az email formátumot és tiltólistát.  
- CAPTCHA ellenőrzést végez.  
- Megnézi, hogy létezik‑e már a felhasználó.  
- Ha nem, létrehoz egy új inaktív fiókot, generál aktiváló tokent, és visszaadja az aktiváló linket (fejlesztési célra).  
- Éles környezetben az aktiváló linket e‑mailben küldenénk ki, itt elég logolni vagy kommentben jelezni.

Összegzés
- **Mi változott?**
  - A `header()` és `require_once db.php` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  
  - Az aktiváló link visszaadása csak fejlesztési célra történik, élesben nem.  

- **Miért jobb így?**
  - Egységes hibakezelés és válaszformátum → frontend mindig ugyanazt kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → modulonként tiszta, egységes struktúra.


*/
