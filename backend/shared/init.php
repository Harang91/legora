<?php
// Központi inicializáló fájl
// Minden endpoint elején csak ezt kell behúzni:
// require_once __DIR__ . '/../shared/init.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

/* //CORS headerek
//header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); */

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://127.0.0.1:5500') {
  header("Access-Control-Allow-Origin: $origin");
  header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Ha preflight OPTIONS kérés érkezik, válaszolj üresen és állj le
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// 1. Alap konfiguráció
require_once __DIR__ . '/../config/db.php'; // PDO kapcsolat

// 2. Core modulok
require_once __DIR__ . '/../core/session.php';   // session indítás, login/logout
require_once __DIR__ . '/../core/security.php';  // input tisztítás, jogosultság, token

// 3. Shared modulok
require_once __DIR__ . '/response.php';       // successResponse(), errorResponse()
require_once __DIR__ . '/validation.php';     // validateEmail(), validatePassword(), stb.
require_once __DIR__ . '/lego_helpers.php';   // getLegoData(), getMultipleLegoData()
/* 
Használat minden endpointban

Minden REST API fájl elején elég ennyi:

```php
require_once __DIR__ . '/../shared/init.php';
```

Ez automatikusan:
- elindítja a session‑t,
- létrehozza a PDO kapcsolatot,
- betölti az összes közös segédfüggvényt,
- és biztosítja az egységes válaszformátumot.

---

## 🎯 Összegzés
- Az `init.php` a projekt gerince, minden endpoint ezt használja.  
- A `shared/` mappában a helye, mert a `response.php`, `validation.php`, `lego_helpers.php` is itt van.  
- Ezzel a megoldással a projekt moduláris, átlátható és könnyen karbantartható lesz.



//CORS headerek:

  Engedélyezi, hogy a böngésző más originről (pl. `127.0.0.1:5500` → VSCode Live Server) is küldhessen kérést a localhost‑on futó XAMPP backendhez.  
- Így a fetch hívás nem dob többé NetworkError‑t, hanem ténylegesen eléri a PHP endpointot.

### Fontos apróságok
- **Fejlesztéshez jó**: `Access-Control-Allow-Origin: *` teljesen nyitott, így minden frontendről engedi a kérést.  
- **Éles környezetben**: érdemes szűkíteni, pl. csak a saját domainre:  
  ```php
  header("Access-Control-Allow-Origin: https://sajatfrontend.hu");
  ```
- Az `OPTIONS` metódus engedélyezése azért kell, mert a böngésző először egy „preflight” kérést küld, mielőtt a POST‑ot ténylegesen elküldi.

---

### ✅ Összefoglalva
Igen, ha ezt bemásolod az `init.php` elejére, akkor a frontend fetch POST kérései átmennek, és megszűnik a `NetworkError`.  

👉 Ha szeretnéd, mutatok egy rövid ellenőrző lépést, amivel azonnal látod, hogy a CORS headerek tényleg működnek (pl. böngésző DevTools → Network fül).


preflight OPTIONS

Most már tisztán látszik, mi történik, András 🙂  

### Miért kapsz kétféle hibát egyszerre?
1. **`NetworkError when attempting to fetch resource`**  
   - Ez a böngésző fetch hibája. Akkor dobja, ha a kérés nem jut el a szerverig, vagy a szerver nem engedi (CORS).  
   - Mivel a válaszban mégis megjelenik a JSON (`Érvénytelen kérés (csak POST engedélyezett)`), valójában a szerver válaszolt → tehát a fetch elérte a PHP‑t.  
   - A `NetworkError` ilyenkor azért jelenhet meg, mert a böngésző nem tudja rendesen feldolgozni a választ (pl. CORS headerek hiányoznak).

2. **`Érvénytelen kérés (csak POST engedélyezett)`**  
   - Ez a backend logikája. Akkor jön, ha a kérés **nem POST**, hanem GET vagy OPTIONS.  
   - A böngésző fetch POST előtt gyakran küld egy **OPTIONS preflight** kérést. Ha erre nincs válasz engedélyezve, a backend visszadobja hibával.

---

### 🛠 Megoldás
Az `init.php` elejére tedd be a CORS kezelést, de egészítsd ki az OPTIONS kezelésével is:

```php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Ha preflight OPTIONS kérés érkezik, válaszolj üresen és állj le
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
```

### Mit érünk el vele?
- A böngésző preflight OPTIONS kérése **200 OK** választ kap → nem dob többé `NetworkError`‑t.  
- A tényleges POST kérés átmegy, és a backend feldolgozza.  
- A GET kérések továbbra is hibát adnak, ahogy kell.

---

👉 Ha ezt beírod az `init.php` elejére, a frontend fetch POST hívásod már nem fog `NetworkError`‑t dobni, hanem ténylegesen megkapja a JSON választ a login endpointtól.  

Szeretnéd, hogy mutassak egy rövid példát, hogyan néz ki a fetch POST hívás a frontendben, ha minden rendben van (helyes body + válasz megjelenítés)?



*/