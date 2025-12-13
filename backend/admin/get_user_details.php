<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * get_user_details.php
 * ---------------------
 * Admin funkció: egy adott felhasználó részletes adatainak lekérése.
 * - Csak GET metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Visszaadja a felhasználó alapadatait és hirdetéseit.
 * - Hibakezelést tartalmaz: hiányzó paraméter, nem létező user, rossz metódus.
 */

// Csak GET kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    errorResponse("Csak GET metódus engedélyezett.");
}

// Ellenőrizzük, hogy van-e aktív admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}

// Paraméter ellenőrzése
$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználó azonosító (id).");
}

try {
    // Lekérdezzük a felhasználót
    $stmt = $pdo->prepare("
        SELECT id, username, email, role, is_active, created_at 
        FROM users 
        WHERE id = :id 
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        errorResponse("Nem található felhasználó ezzel az ID-val.");
    }

    // Lekérdezzük a felhasználó hirdetéseit
    $stmtListings = $pdo->prepare("
        SELECT id, item_type, item_id, price, item_condition, deleted_at 
        FROM listings 
        WHERE user_id = :id
    ");
    $stmtListings->execute(['id' => $id]);
    $listings = $stmtListings->fetchAll(PDO::FETCH_ASSOC);

    // JSON válasz összeállítása
    successResponse("Felhasználó részletes adatai lekérve.", [
        "user" => $user,
        "listings" => $listings
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}



/* 
### Cél  
A `get_user_details.php` endpoint feladata, hogy **az adminisztrátor számára egy adott felhasználó részletes adatait adja vissza**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja a felhasználó alapadatait (ID, username, email, role, is_active, created_at).  
- Visszaadja a felhasználóhoz tartozó hirdetéseket (id, item_type, item_id, price, item_condition, deleted_at).  
- Hibakezelést tartalmaz: hiányzó paraméter, nem létező user, rossz metódus.  

---

###  Összegzés
- **Mi változott?**
  - Javítva a hibás változók (`$SERVER` → `$_SERVER`, `isactive` → `is_active`, `createdat` → `created_at`, `userid` → `user_id`, `title` → `item_type/item_id/description`).  
  - Beépítve az admin session ellenőrzés → csak bejelentkezett admin férhet hozzá.  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`401`, `404`, `405`, `422`, `500`).  

- **Miért jobb így?**
  - Biztonságos → csak admin jogosultsággal érhető el.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - Valós adatbázis mezők → a dokumentáció és tesztek életszerűek lesznek.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok, részletes adatlekérés.  


régi:
Összefoglalás
Ez az get_user_details.php script az admin modulban egy adott felhasználó részletes adatait adja vissza:  
- Felhasználó alapadatai: ID, username, email, role, isactive, createdat.  
- A felhasználóhoz tartozó hirdetések listája (id, title, price, deleted_at).  
- Hibakezelést tartalmaz: hiányzó paraméter, nem létező user, rossz metódus.  
- JSON formátumban adja vissza az adatokat, így a frontend könnyen meg tudja jeleníteni. */
