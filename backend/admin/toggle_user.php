<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * toggle_user.php
 * ----------------
 * Admin funkció: felhasználó aktív/inaktív státuszának váltása.
 * - Csak POST metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Ellenőrzi, hogy létezik-e a felhasználó.
 * - Ha aktív volt, inaktiválja; ha inaktív volt, aktiválja.
 * - JSON választ ad vissza: success vagy error.
 */

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST metódus engedélyezett.");
}

// Ellenőrizzük, hogy van-e aktív admin session
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    errorResponse("Nincs aktív admin session.");
}

// A frontendnek küldenie kell a user ID-t
$userId = $_POST['id'] ?? null;
if (!$userId) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználó azonosító (id).");
}

try {
    // Lekérdezzük az aktuális állapotot (is_active)
    $stmt = $pdo->prepare("
        SELECT id, username, is_active 
        FROM users 
        WHERE id = :id
    ");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        errorResponse("Nem található felhasználó ezzel az ID-val.");
    }

    // Az új állapot: ha aktív volt, inaktiváljuk; ha inaktív volt, aktiváljuk
    $newStatus = $user['is_active'] == 1 ? 0 : 1;

    // Frissítjük az adatbázisban
    $updateStmt = $pdo->prepare("
        UPDATE users 
        SET is_active = :newStatus 
        WHERE id = :id
    ");
    $updateStmt->execute([
        'newStatus' => $newStatus,
        'id' => $userId
    ]);

    successResponse(
        $newStatus == 1 ? "A felhasználó aktiválva lett." : "A felhasználó inaktiválva lett.",
        [
            "user_id" => (int)$userId,
            "username" => $user['username'],
            "is_active" => $newStatus
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/* 
### Cél  
A `toggle_user.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy felhasználó aktív/inaktív státuszának váltását**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a felhasználó.  
- Ha aktív volt, inaktiválja (`is_active = 0`), ha inaktív volt, aktiválja (`is_active = 1`).  
- JSON választ ad vissza: `success` vagy `error`.  

---
###  Összegzés
- **Mi változott?**
  - Javítva a hibás változó (`$SERVER` → `$_SERVER`).  
  - Beépítve az admin session ellenőrzés → csak bejelentkezett admin férhet hozzá.  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`401`, `404`, `405`, `422`, `500`).  
  - A válaszban most már a `username` is visszajön, így a frontend könnyebben tudja megjeleníteni.  

- **Miért jobb így?**
  - Biztonságos → csak admin jogosultsággal érhető el.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok, státuszváltás. 




Összefoglalás
Ez a toggle_user.php script az admin modul része, és a felhasználók állapotát kezeli.  
- Kap egy user_id paramétert POST metódusban.  
- Lekérdezi az adott user aktuális állapotát (is_active).  
- Ha aktív volt, inaktiválja (isactive=0), ha inaktív volt, aktiválja (isactive=1).  
- Visszaad egy JSON választ, amely tartalmazza az új állapotot és egy üzenetet.  
- Hibakezelést is tartalmaz: ha nincs ilyen user, vagy hiányzik az ID, akkor status: error választ küld.  

Ez az endpoint lehetővé teszi, hogy az admin egy kattintással aktiválja vagy inaktiválja a felhasználókat.
 */