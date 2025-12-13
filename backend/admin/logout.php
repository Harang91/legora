<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * logout.php
 * ----------------
 * Admin kijelentkezés kezelése.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Ha van, törli a session változókat, a cookie-t, és lezárja a session-t.
 * - JSON választ ad vissza: success ha sikeres, error ha nem volt aktív session.
 */

// Csak POST metódus engedélyezett a biztonság érdekében
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// Ellenőrizzük, hogy van-e aktív admin session
if (isset($_SESSION['admin_id'])) {
    // Minden session változó törlése
    $_SESSION = [];

    // Session cookie törlése (ha van)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Session lezárása
    session_destroy();

    successResponse("Sikeres kijelentkezés.");
} else {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}



/* 
### Cél  
A `logout.php` endpoint feladata, hogy **biztonságos kijelentkezést biztosítson az adminisztrátorok számára**.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ha van, törli a session változókat, a cookie‑t, és lezárja a session‑t.  
- JSON választ ad vissza: `success` ha sikeres, `error` ha nem volt aktív session.  

---
### Összegzés
- **Mi változott?**
  - Az `init.php` kezeli a session indítást → nem kell külön `session_start()`.  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`401`, `405`).  
  - Csak POST metódus engedélyezett → biztonságosabb, mint GET.  

- **Miért jobb így?**
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - Biztonságos kijelentkezés → session és cookie teljes törlése.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok.  





régi:
 Összefoglalás
Ez a logout.php script az admin modulban a kijelentkezést kezeli:  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ha van, törli a session változókat, a cookie‑t, és lezárja a session‑t.  
- JSON választ ad vissza: success ha sikeres, error ha nem volt aktív session.  

Ez az endpoint biztosítja, hogy az admin biztonságosan ki tudjon lépni a rendszerből, és ne maradjon nyitva a session.
*/