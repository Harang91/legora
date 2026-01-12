<?php

require_once __DIR__ . '/../shared/init.php';

// Csak POST metódus engedélyezett
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// Aktív admin session ellenőrzése
if (isset($_SESSION['admin_id'])) {

    // Session változók törlése
    $_SESSION = [];

    // Session cookie törlése
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
