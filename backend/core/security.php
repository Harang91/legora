<?php

// Biztonsági segédfüggvények

function sanitizeInput(string $data): string
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

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

function requireAdmin(): bool
{
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        securityError("Nincs admin jogosultság");
        return false;
    }
    return true;
}

function validateToken(?string $token): bool
{
    if (!$token || !isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        securityError("Érvénytelen vagy hiányzó token");
        return false;
    }
    return true;
}
