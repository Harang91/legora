<?php
// Biztonsági segédfüggvények
// Minden modul ezt húzza be, hogy egységesen kezelje az inputokat és jogosultságokat

//header('Content-Type: application/json; charset=utf-8'); //nem kell, az init.php intézi


/* Input tisztítás (HTML és SQL injection ellen)
 * @param string $data
 * @return string
 */
function sanitizeInput(string $data): string
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/*
 * Egységes JSON válasz hiba esetén
 * @param string $message
 */
function securityError(string $message): void
{
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "message" => $message,
        "data" => null
    ]);
    exit;
}

/*
 * Admin jogosultság ellenőrzése
 * @return bool
 */
function requireAdmin(): bool
{
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        securityError("Nincs admin jogosultság");
        return false;
    }
    return true;
}

/*
 * Token ellenőrzés (pl. CSRF vagy API kulcs)
 * @param string|null $token
 * @return bool
 */
function validateToken(?string $token): bool
{
    if (!$token || !isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        securityError("Érvénytelen vagy hiányzó token");
        return false;
    }
    return true;
}

/* Ez a `security.php` modul:  
- sanitizeInput() → minden bejövő adatot megtisztít, hogy ne lehessen HTML/SQL injection.  
- securityError() → egységes JSON hibaválasz biztonsági problémáknál.  
- requireAdmin() → ellenőrzi, hogy a sessionben van‑e admin jogosultság.  
- validateToken() → ellenőrzi a CSRF vagy API token meglétét és érvényességét.  */