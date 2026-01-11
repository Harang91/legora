<?php

// Session kezelés

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    return [
        "user_id"  => $_SESSION['user_id'],
        "username" => $_SESSION['username'] ?? null,
        "email"    => $_SESSION['email'] ?? null
    ];
}

function setUserSession(int $userId, string $username, string $email): void
{
    $_SESSION['user_id']  = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['email']    = $email;
}

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
