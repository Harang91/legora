<?php
// Egységes input validációs függvények
// Minden modul ezt a fájlt húzza be az init.php-n keresztül

//header('Content-Type: application/json; charset=utf-8');   //nem kell, az init.php intézi

/*
 * Email formátum ellenőrzése
 * @param string $email
 * @return bool
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/*
 * Jelszó erősség ellenőrzése
 * - Minimum 8 karakter
 * - Legalább egy szám
 * - Legalább egy betű
 * @param string $password
 * @return bool
 */
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

/*
 * Kötelező mezők ellenőrzése
 * @param array $data - bejövő adatok
 * @param array $requiredFields - kötelező mezők listája
 * @return array - hiányzó mezők listája
 */
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

/*
 * Captcha ellenőrzés (példa)
 * @param string $captchaInput
 * @return bool
 */
function validateCaptcha(string $captchaInput): bool
{
    // Példa: session-ben tárolt captcha összehasonlítása
    return isset($_SESSION['captcha']) && $captchaInput === $_SESSION['captcha'];
}

/*
 * Telefonszám ellenőrzése
 * - Csak számok, szóköz, + és - engedélyezett
 * - Minimum 6 karakter
 * @param string $phone
 * @return bool
 */
function validatePhone(string $phone): bool
{
    return preg_match('/^[0-9+\-\s]{6,}$/', $phone) === 1;
}

/*
 * Lakcím ellenőrzése
 * - Minimum 5 karakter
 * - Max 255 karakter
 * @param string $address
 * @return bool
 */
function validateAddress(string $address): bool
{
    $len = strlen(trim($address));
    return $len >= 5 && $len <= 255;
}



/* ---

##  Összegzés
Ez a `validation.php` modul:  
- Egységes input ellenőrzést biztosít minden endpointban.  
- Függvények:  
  - `validateEmail()` → email formátum.  
  - `validatePassword()` → jelszó erősség.  
  - `validateRequiredFields()` → kötelező mezők megléte.  
  - `validateCaptcha()` → captcha ellenőrzés.  
  - `validatePhone()` → telefonszám ellenőrzés.
  - `validateAddress()` → lakcím ellenőrzés.
- Így minden modulban elég csak hívni:  
  ```php
  $missing = validateRequiredFields($_POST, ["username", "password"]);
  if (!empty($missing)) {
      errorResponse("Hiányzó kötelező mezők", ["missing" => $missing]);
  }
  ``` */
