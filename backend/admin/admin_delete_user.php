<?php
// Központi inicializáló fájl betöltése (DB, session, security, response, helpers)
require_once __DIR__ . '/../shared/init.php';

/**
 * admin_delete_user.php
 * ----------------------
 * Admin funkció: felhasználó soft delete (inaktiválás).
 * - Csak POST metódus engedélyezett.
 * - Ellenőrzi, hogy van-e aktív admin session.
 * - Ellenőrzi, hogy létezik-e a felhasználó.
 * - Admin felhasználót nem engedi törölni.
 * - Soft delete: is_active = 0.
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
        SELECT id, username, role 
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

    // Admin felhasználót nem törlünk
    if ($user['role'] === 'admin') {
        http_response_code(403);
        errorResponse("Admin felhasználó nem törölhető.");
    }

    // Soft delete: is_active = 0
    $stmtDelete = $pdo->prepare("
        UPDATE users 
        SET is_active = 0 
        WHERE id = :id
    ");
    $stmtDelete->execute(['id' => $id]);

    successResponse("Felhasználó sikeresen inaktiválva.", [
        "user_id" => (int)$id,
        "username" => $user['username']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}

/* 
### Cél  
Az `admin_delete_user.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy felhasználó inaktiválását (soft delete)**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a felhasználó.  
- Admin felhasználót nem engedi törölni.  
- Soft delete módon inaktiválja a felhasználót (`is_active = 0`).  
- JSON választ ad vissza: `success` vagy `error`.  

---
###  Összegzés
- **Mi változott?**
  - Javítva a hibás változó (`$SERVER` → `$_SERVER`).  
  - Beépítve az admin session ellenőrzés → csak bejelentkezett admin férhet hozzá.  
  - Egységes hibakezelés: `errorResponse()` és `successResponse()` függvények.  
  - HTTP státuszkódok pontosabb használata (`401`, `403`, `404`, `405`, `422`, `500`).  

- **Miért jobb így?**
  - Biztonságos → csak admin jogosultsággal érhető el.  
  - Admin felhasználó védett → nem törölhető.  
  - Egységes JSON válasz → frontend mindig kiszámítható választ kap.  
  - Vizsgán jól bemutatható → REST alapelvek, session kezelés, hibakódok, soft delete.  





régi:
Összefoglalás
Ez az admin_delete_user.php script az admin modulban a felhasználó törlését/inaktiválását kezeli:  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy létezik-e a felhasználó.  
- Admin felhasználót nem engedi törölni.  
- Soft delete módon inaktiválja a felhasználót (is_active = 0).  
- JSON választ ad vissza: success vagy error.  

Ez jól demonstrálja a jogosultságkezelést és biztonságos törlést.


*/