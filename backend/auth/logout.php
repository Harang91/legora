<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, validation, helpers)
require_once __DIR__ . '/../shared/init.php';

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Érvénytelen kérés (csak POST engedélyezett)");
}

// Ha nincs bejelentkezett user
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív bejelentkezés");
}

// Session törlése
session_unset();
session_destroy();

// Sikeres válasz
successResponse("Sikeres kijelentkezés", null);

/* 
Leírás:
Az `auth/logout.php` végpont felelős a felhasználó kijelentkeztetéséért.  
- Csak **POST** kérést fogad.  
- Ellenőrzi, hogy van‑e aktív session (bejelentkezett user).  
- Ha nincs, hibát ad vissza.  
- Ha van, törli a session‑t és visszaadja a sikeres kijelentkezés üzenetét.  
- A frontendnek nincs szüksége body‑ra, csak a session cookie‑ra, amit a login után a böngésző tárol.

Összegzés
- Mi változott?
  - A `header()` és `session_start()` hívások kikerültek → az `init.php` intézi.  
  - Az `errorResponse()` és `successResponse()` függvények használata → egységes JSON válasz formátum.  
  - A kód rövidebb, tisztább, minden közös logika az `init.php`‑ban van.  

- Miért jobb így?
  - Egységes hibakezelés → frontend mindig ugyanazt a formátumot kapja.  
  - Könnyebb karbantartás → ha változik a session vagy a válasz formátum, csak az `init.php`‑t kell módosítani.  
  - Vizsgán jól bemutatható → „egy sorral betöltjük az init.php‑t, és minden modul ugyanazt a környezetet kapja”. 
  */
