<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * admin_restore_user.php
 * -----------------------
 * Admin funkció: soft delete-elt felhasználó visszaállítása.
 * - Csak POST metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Ellenőrzi, hogy létezik-e a felhasználó.
 * - Ha a felhasználó már aktív, hibát ad vissza.
 * - Soft delete-elt felhasználót visszaállítja (is_active = 1).
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

// Paraméter ellenőrzése
$id = $_POST['id'] ?? null;
if (!$id) {
    http_response_code(422);
    errorResponse("Hiányzik a felhasználó azonosító (id).");
}

try {
    // Ellenőrizzük, hogy létezik-e a felhasználó
    $stmt = $pdo->prepare("
        SELECT id, username, is_active, role 
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

    // Ha már aktív
    if ((int)$user['is_active'] === 1) {
        http_response_code(409);
        errorResponse("A felhasználó nincs inaktiválva, így nem állítható vissza.");
    }

    // Soft delete visszaállítása: is_active = 1
    $stmtRestore = $pdo->prepare("
        UPDATE users 
        SET is_active = 1 
        WHERE id = :id
    ");
    $stmtRestore->execute(['id' => $id]);

    successResponse("Felhasználó sikeresen visszaállítva.", [
        "user_id" => (int)$id,
        "username" => $user['username']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}


/* 
### Cél  
Az `admin_restore_user.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy soft delete‑elt (inaktivált) felhasználó visszaállítását**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a felhasználó.  
- Ha a felhasználó már aktív, hibát ad vissza.  
- Soft delete‑elt felhasználót visszaállítja (`is_active = 1`).  
- JSON választ ad vissza: `success` vagy `error`.  

---
### Összegzés
- **Mi változott?**
  - Javítva a hibás változó (`$SERVER` → `$_SERVER`).  
  - Beépítve az admin session ellenőrzés → csak bejelentkezett admin férhet hozzá.  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`401`, `404`, `405`, `409`, `422`, `500`).  

- **Miért jobb így?**
  - Biztonságos → csak admin jogosultsággal érhető el.  
  - Megakadályozza a felesleges visszaállítást (ha már aktív).  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok, soft delete visszaállítás.  




régi:
Összefoglalás
Ez az admin_restore_user.php script az admin modulban a felhasználó visszaállítását kezeli:  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy létezik‑e a felhasználó.  
- Ha nincs inaktiválva, hibát ad vissza.  
- Soft delete‑elt felhasználót visszaállítja (is_active = 1).  
- JSON választ ad vissza: success vagy error.  

Ez jól demonstrálja a teljes körű user‑kezelést: törlés és visszaállítás.

*/