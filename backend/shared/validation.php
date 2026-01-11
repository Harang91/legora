<?php

// Input validációs függvények

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword(string $password): bool
{
    if (strlen($password) < 8) {
        return false;
    }
    if (!preg_match('/[A-Za-z]/', $password)) {
        return false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    return true;
}

function validateRequiredFields(array $data, array $requiredFields): array
{
    $missing = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $missing[] = $field;
        }
    }
    return $missing;
}

function validateCaptcha(string $captchaInput): bool
{
    return isset($_SESSION['captcha']) && $captchaInput === $_SESSION['captcha'];
}

function validatePhone(string $phone): bool
{
    return preg_match('/^[0-9+\-\s]{6,}$/', $phone) === 1;
}

function validateAddress(string $address): bool
{
    $len = strlen(trim($address));
    return $len >= 5 && $len <= 255;
}
