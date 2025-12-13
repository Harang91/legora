<?php
// Egységes JSON válaszok a REST API-hoz
// Minden modul ezt a fájlt húzza be az init.php-n keresztül

//header('Content-Type: application/json; charset=utf-8');  //nem kell, az init.php intézi

/*
 * Sikeres válasz küldése
 * @param string $message - emberi olvasható üzenet
 * @param mixed $data - opcionális adat (array, object, string, stb.)
 */
function successResponse(string $message, $data = null): void
{
  http_response_code(200);
  echo json_encode([
    "status" => "success",
    "message" => $message,
    "data" => $data
  ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  exit;
}

/*
 * Hibás válasz küldése
 * @param string $message - hibaüzenet
 * @param mixed $data - opcionális adat (pl. részletes hiba)
 * @param int $code - HTTP státuszkód (alapértelmezés: 400)
 */
function errorResponse(string $message, $data = null, int $code = 400): void
{
  http_response_code($code);
  echo json_encode([
    "status" => "error",
    "message" => $message,
    "data" => $data
  ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  exit;
}

/* 
 Összegzés
Ez a `response.php` modul:  
- Egységes JSON formátumot biztosít minden endpointban.  
- Két fő függvény van:  
  - `successResponse()` → sikeres műveletekhez.  
  - `errorResponse()` → hibákhoz, HTTP státuszkóddal.  
- Így minden modulban elég csak hívni:  
  ```php
  successResponse("Hirdetés létrehozva", $listingData);
  // vagy
  errorResponse("Érvénytelen kérés", null, 405
*/