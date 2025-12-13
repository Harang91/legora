<?php
$dsn = "mysql:host=localhost;dbname=legora;charset=utf8mb4;port=3307";
$user = "root"; // vagy a saját adatbázis felhasználód
$pass = "";     // ha van jelszó, ide írd be

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die(json_encode([
        "status" => "error",
        "message" => "Adatbázis kapcsolat hiba: " . $e->getMessage(),
        "data" => null
    ]));
}
/* 

 A `config/db.php` a projekt **adatbázis‑kapcsolati központja**. Ez az a kis fájl, amit minden modul (`create_listing.php`, `get_listings.php`, `update_listing.php`, `delete_listing.php`) `require_once`‑szal behúz, és így nem kell minden egyes kódban újra és újra leírni az adatbázis‑kapcsolódás logikáját.

---

összefoglaló

- **Adatbázis beállítások**: tartalmazza a hostot, adatbázis nevét, felhasználót, jelszót.  
- **PDO kapcsolat létrehozása**: létrehoz egy `$pdo` objektumot, amit a többi modul használ SQL lekérdezésekhez.  
- **Hibakezelés**: általában `PDO::ERRMODE_EXCEPTION` beállítással, hogy azonnal kivételt dobjon, ha valami gond van.  
- **Karakterkódolás**: legtöbbször `utf8mb4`, hogy minden ékezet és emoji is rendben működjön.  
- **Központi újrafelhasználhatóság**: ha változik a DB jelszó vagy szerver, csak itt kell átírni, nem az összes modulban.


Tipikus tartalom (példa)

```php

    <?php
    $host = 'localhost';
    $db   = 'legora';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // hibakezelés
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // asszociatív tömb
        PDO::ATTR_EMULATE_PREPARES   => false,                  // natív prepared statement
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die("Adatbázis kapcsolódási hiba: " . $e->getMessage());
    }
```


Röviden
A `config/db.php` a **„kapu” az adatbázishoz**:  
- minden modul innen kapja a `$pdo` kapcsolatot,  
- egységes hibakezelést és karakterkódolást biztosít,  
- és központi helyen tartja a DB‑beállításokat.  

---

.




*/