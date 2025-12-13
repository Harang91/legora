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
$emailOrUsername = trim($input['email_or_username'] ?? '');
$password        = $input['password'] ?? '';

if ($emailOrUsername === '' || $password === '') {
    http_response_code(422);
    errorResponse("Minden mező kitöltése kötelező (email_or_username, password)");
}

try {
    // Felhasználó lekérése az adatbázisból
    $stmt = $pdo->prepare("
        SELECT id, username, email, password_hash, is_active 
        FROM users 
        WHERE email = ? OR username = ?
    ");
    $stmt->execute([$emailOrUsername, $emailOrUsername]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        errorResponse("Hibás felhasználónév vagy e‑mail");
    }

    // Ellenőrizzük, hogy aktív‑e a fiók
    if ((int)$user['is_active'] !== 1) {
        http_response_code(403);
        errorResponse("A fiók nincs aktiválva. Kérlek, ellenőrizd az e‑mail fiókodat.");
    }

    // Jelszó ellenőrzés
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        errorResponse("Hibás jelszó");
    }

    // Session beállítása
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    // Sikeres válasz
    successResponse("Sikeres bejelentkezés", [
        "user_id" => $user['id'],
        "username" => $user['username'],
        "email" => $user['email']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/*


Összegzés
- **Mi változott?**
  - A `header()` és `session_start()` hívások kikerültek → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Könnyebb karbantartás → ha változik a DB/session/security, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → „egy sorral betöltjük az init.php‑t, és minden modul ugyanazt a környezetet kapja”.


Régi:
Speciális logika
- Double opt‑in: csak is_active = 1 felhasználók léphetnek be.
- Session kezelés: sikeres login után a $_SESSION['user_id'] és $_SESSION['username'] beállításra kerül.
- Biztonság: password_verify() ellenőrzi a hash‑elt jelszót. 

- URL: POST /auth/login.php
- Content-Type: application/json
- Leírás: Bejelentkezés e‑mail vagy felhasználónév + jelszó párossal. Csak aktivált fiókok engedélyezettek.
- fiók aktiválás: elméletileg a felhasználónak e-mailjére küldött megerősítő linkre kattintva, de itt a legora adatbázisban:
- létrehozunk egy új felhasználót
- users/ verify_token oszlopban katt a módósításra és kimásolod a generált tokent.
- GET http://localhost/legora/auth/verify.php?token=    és ide bemásolod.  
- ez aktiválja a felhasználót.


*/