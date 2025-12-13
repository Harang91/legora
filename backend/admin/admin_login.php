<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * admin_login.php
 * ----------------
 * Admin bejelentkezés kezelése.
 * - Csak POST metódus engedélyezett.
 * - Ellenőrzi, hogy a felhasználó létezik, admin szerepkörben van, és aktív.
 * - Jelszó ellenőrzése password_verify segítségével.
 * - Siker esetén session létrehozása és JSON válasz.
 * - Hibás esetekben egységes errorResponse().
 */

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// Paraméterek ellenőrzése
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;

if (!$username || !$password) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználónév vagy jelszó.");
}

try {
    // Lekérdezzük a felhasználót az adatbázisból
    $stmt = $pdo->prepare("
        SELECT id, username, password, role, is_active 
        FROM users 
        WHERE username = :username 
        LIMIT 1
    ");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        errorResponse("Nincs ilyen felhasználó.");
    }

    // Ellenőrizzük, hogy admin-e és aktív-e
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        errorResponse("Nincs admin jogosultság.");
    }

    if ((int)$user['is_active'] === 0) {
        http_response_code(403);
        errorResponse("A felhasználó inaktív.");
    }

    // Jelszó ellenőrzése
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        errorResponse("Hibás jelszó.");
    }

    // Ha minden rendben, létrehozunk egy session-t
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];

    successResponse("Sikeres admin bejelentkezés.", [
        "admin_id" => (int)$user['id'],
        "username" => $user['username']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/*
### Cél  
Az `admin_login.php` endpoint feladata, hogy **biztonságos bejelentkezést biztosítson az adminisztrátorok számára**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy a felhasználó létezik, admin szerepkörben van, és aktív.  
- A jelszót `password_verify` segítségével ellenőrzi (feltételezve, hogy `password_hash`-al van tárolva).  
- Ha minden rendben, létrehoz egy session-t, és visszaadja a sikeres bejelentkezés üzenetét JSON formátumban.  
- Hibás esetekben mindig `status: error` választ küld, egyértelmű üzenettel.  

---
###  Összegzés
- **Mi változott?**
  - Javítva a hibás változónevek (`$SERVER` → `$_SERVER`, `$SESSION` → `$_SESSION`).  
  - A `header()` és `session_start()` kikerült → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - HTTP státuszkódok pontosabb használata (`401`, `403`, `404`, `405`, `422`, `500`).  

- **Miért jobb így?**
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Biztonságos bejelentkezés → csak admin jogosultságú, aktív felhasználó léphet be.  
  - Vizsgán jól bemutatható → POST metódus, validáció, jogosultság ellenőrzés, session kezelés.  






RÉGI:
Összefoglalás
Ez az admin_login.php script az admin modulban a bejelentkezést kezeli:  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy létezik-e a felhasználó, admin szerepkörben van-e, és aktív-e.  
- A jelszót passwordverify segítségével ellenőrzi (feltételezve, hogy passwordhash-al van tárolva).  
- Ha minden rendben, létrehoz egy session-t, és visszaadja a sikeres bejelentkezés üzenetét JSON formátumban.  
- Hibás esetekben mindig status: error választ küld, egyértelmű üzenettel.  

Ez az endpoint biztosítja, hogy az admin funkciókhoz csak jogosult felhasználók férjenek hozzá,
 */