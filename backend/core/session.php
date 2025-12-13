<?php
// Egységes session kezelés
// Minden modul ezt a fájlt húzza be, így nem kell mindenhol külön session_start()

//header('Content-Type: application/json; charset=utf-8');  //nem kell, az init.php intézi

// Biztonságos session indítás
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ellenőrzi, hogy van-e bejelentkezett felhasználó
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Visszaadja a bejelentkezett felhasználó adatait
 * @return array|null
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    return [
        "user_id"   => $_SESSION['user_id'],
        "username"  => $_SESSION['username'] ?? null,
        "email"     => $_SESSION['email'] ?? null
    ];
}

/**
 * Bejelentkezéskor beállítja a session adatokat
 * @param int $userId
 * @param string $username
 * @param string $email
 */
function setUserSession(int $userId, string $username, string $email): void
{
    $_SESSION['user_id']  = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['email']    = $email;
}

/**
 * Kilépteti a felhasználót (logout)
 */
function destroySession(): void
{
    $_SESSION = [];
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
    session_destroy();
}

/* 
 Összegzés
Ez a session.php modul:
- Egységesen indítja a sessiont minden API hívásnál.  
- Függvényeket ad: isLoggedIn(), getCurrentUser(), setUserSession(), destroySession().  
- Így minden más modul (pl. login.php, logout.php, get_user.php) egyszerűen hivatkozhat rá, és mindig ugyanazt a JSON logikát használhatja.  

A session egy átmeneti adatjegy vagy dosszié, amit a szerver készít egy felhasználónak, amikor belép a rendszerbe.
A session egy szerver oldali tárolt adatcsomag, ami ideiglenesen megjegyzi a felhasználó állapotát, amíg be van jelentkezve.


*/