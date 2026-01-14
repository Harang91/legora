##  Backend összefoglaló – A LEGORA rendszer kiszolgáló oldala

A LEGORA projekt backendje PHP nyelven készült, és REST-alapú API-ként működik. A célja, hogy kiszolgálja a frontend felület igényeit: felhasználói műveletek, hirdetések kezelése, rendeléslogika, adminisztráció és LEGO metaadatok elérése. A rendszer **moduláris felépítésű**, jól tagolt mappastruktúrával és újrafelhasználható komponensekkel.

---

**összefoglaló a backend felépítéséről és működéséről**:

---
A LEGORA projekt backendje egy PHP-alapú, REST-szerű API, amely a LEGO piactér működésének minden kulcselemét kiszolgálja: a felhasználói regisztrációtól kezdve a hirdetések kezelésén, kosárfunkción és rendeléseken át egészen az adminisztrációs műveletekig. A rendszer célja, hogy egy megbízható, biztonságos és jól skálázható alapot biztosítson a frontend számára, miközben a háttérben gondoskodik az adatok épségéről és az üzleti logika érvényesítéséről.
A backend moduláris felépítésű, jól strukturált mappaszerkezetre épül, ahol minden funkció külön fájlban található. Ez nemcsak az átláthatóságot segíti, hanem lehetővé teszi a csapaton belüli párhuzamos fejlesztést is. A központi  fájl minden végpont elején betöltődik, és gondoskodik az alapbeállításokról, a biztonsági ellenőrzésekről, az adatbázis-kapcsolatról és a közös segédfüggvények elérhetőségéről.
A backend működése során minden adatot a MySQL-alapú relációs adatbázisból olvas és ír, miközben session-alapú hitelesítést alkalmaz. A válaszok egységes JSON formátumban érkeznek, így a frontend könnyen tudja kezelni őket. A rendszer CORS-kompatibilis, így fejlesztés közben is gond nélkül kommunikál a frontenddel.
A LEGORA backend nemcsak egy működő rendszer, hanem egy jól átgondolt architektúra, amely készen áll a bővítésre – akár külső API integrációval (pl. Rebrickable), akár új funkciókkal, mint például képfeltöltés, üzenetküldés vagy statisztikai modulok.
## Backend összefoglaló – A LEGORA rendszer kiszolgáló oldala

A LEGORA projekt backendje PHP nyelven készült, és REST-alapú API-ként működik. A célja, hogy kiszolgálja a frontend felület igényeit: felhasználói műveletek, hirdetések kezelése, rendeléslogika, adminisztráció és LEGO metaadatok elérése. A rendszer **moduláris felépítésű**, jól tagolt mappastruktúrával és újrafelhasználható komponensekkel.

-----------------------------------------------------------------------------------------------------------------------------

## Mappastruktúra és fő modulok

### `config/` – Központi beállítások
- `db.php`: Az adatbázis-kapcsolatot inicializáló fájl. PDO-t használ, biztonságos, paraméterezett lekérdezésekkel.

### `auth/` – Felhasználói hitelesítés
- Regisztráció, bejelentkezés, email-ellenőrzés és kijelentkezés.
- Minden művelet session-alapú, és a `security.php` middleware-rel védett.

### `users/` – Felhasználói profilkezelés
- Lekérdezés és módosítás.
- Jogosultság-ellenőrzés beépítve.

### `listings/` – Hirdetések kezelése
- Hirdetés létrehozása, módosítása, törlése, visszaállítása.
- Keresőfunkció kulcsszóra, típusra, árra.
- A `restore_listing.php` csak admin által elérhető.

### `cart/` – Kosárkezelés
- Hirdetések kosárba helyezése, eltávolítása, lekérdezése.
- Minden művelet felhasználóhoz kötött.

### `orders/` – Rendeléslogika
- Rendelés leadása (`checkout.php`), státuszfrissítés, rendeléslista lekérdezése.
- A `order_status_history` tábla naplózza a változásokat.

### `ratings/` – Értékelések
- Felhasználók közötti értékelés (1–5 csillag + szöveg).
- Egy felhasználó csak egyszer értékelhet egy másikat.

### `admin/` – Adminisztrációs funkciók
- Felhasználók és hirdetések listázása, törlése, visszaállítása.
- Statisztikák lekérdezése (`get_stats.php`, `get_all_stats.php`).
- Admin jogosultság ellenőrzése minden fájlban kötelező.

### `shared/` – Közös segédfüggvények
- `response.php`: Egységes JSON válaszformátum.
- `validation.php`: Email, jelszó, captcha ellenőrzés.
- `lego_helpers.php`: Statikus LEGO metaadatok lekérdezése.
- `init.php`: Minden fájl ezt tölti be elsőként – inicializálja a sessiont, adatbázist, validációt és válaszkezelést.

### `core/` – Alap logika, middleware
- `session.php`: Sessionkezelés, időkorlát, automatikus lejárat.
- `security.php`: Jogosultság-ellenőrzés, admin szintű hozzáférés.

### `frontend/` – Teszteléshez használt HTML/JS fájlok
- Egyszerű regisztrációs, bejelentkezési és listázó felületek.
- `app.js`: AJAX-hívások a backend API-khoz.

### `docs/` – API dokumentáció
- `api_endpoints.md`: A frontend csapat számára készült, minden végpont paraméterezésével és válaszaival.

### Gyökérfájlok
- `index.php`: Alapértelmezett belépési pont.
- `sql_fuggvenyek.php`: Egyedi SQL-függvények, pl. statisztikákhoz.

---

## Biztonság és struktúra

- **Session-alapú hitelesítés**: minden felhasználói és admin művelet sessionhöz kötött.
- **Input validáció**: minden bemenetet ellenőrzünk (email, jelszó, ID-k).
- **Egységes válaszformátum**: minden API JSON-t ad vissza, `success`, `message`, `data` mezőkkel.
- **Jogosultságkezelés**: külön middleware ellenőrzi, hogy a felhasználó be van-e jelentkezve, és admin-e.

---

## Backend–Frontend kapcsolat

- A frontend AJAX-hívásokkal kommunikál a backenddel.
- A `docs/api_endpoints.md` fájl tartalmazza az összes végpontot, paraméterekkel és válaszokkal.
- A backend REST-szerűen működik: minden funkció külön `.php` fájl, jól dokumentált és tesztelhető.

---

## Összegzés

A LEGORA backend egy jól strukturált, moduláris rendszer, amely lefedi egy LEGO piactér összes alapfunkcióját: regisztráció, hirdetéskezelés, kosár, rendelés, értékelés, adminisztráció. A fájlstruktúra átlátható, a kód újrafelhasználható, a biztonság és validáció pedig minden szinten biztosított. A rendszer könnyen bővíthető, akár külső API integrációval (pl. Rebrickable), akár új funkciókkal (pl. képfeltöltés, üzenetküldés).


--------------------------------------------------------

## `init.php` – A rendszer központi inicializáló fájlja

Az `init.php` minden egyes backend végpont elején betöltésre kerül. Ez a fájl felelős azért, hogy **minden szükséges beállítás, biztonsági ellenőrzés és segédfüggvény** automatikusan elérhető legyen. Olyan, mint egy „központi kapcsolótábla”, ami előkészíti a terepet minden más művelethez.

### 🔧 1. **Alap beállítások**
```php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
```
- Kikapcsolja a hibák megjelenítését a felhasználó felé (biztonsági okokból).
- A háttérben viszont minden hibát naplóz (`E_ALL`), így fejlesztés közben nyomon követhetők.
- A válasz formátuma mindig JSON lesz, UTF-8 kódolással – ez biztosítja az egységes kommunikációt a frontenddel.

---

### 2. **CORS-kezelés (Cross-Origin Resource Sharing)**
```php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://127.0.0.1:5500') {
  header("Access-Control-Allow-Origin: $origin");
  header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
```
- Ez a rész engedélyezi, hogy a frontend (pl. a `localhost:5500`-ról futó fejlesztői felület) hozzáférjen a backendhez.
- Beállítja, hogy milyen HTTP metódusokat és fejlécet engedélyezünk.
- A `Credentials: true` lehetővé teszi a session cookie-k küldését – ez kell a bejelentkezéshez.

---

### 3. **Preflight válasz (OPTIONS kérés kezelése)**
```php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}
```
- A böngészők bizonyos kérések előtt küldenek egy „előzetes” (preflight) OPTIONS kérést.
- Ez a rész azonnal válaszol rá, hogy ne fusson le feleslegesen a teljes backend logika.

---

### 4. **Alapmodulok betöltése**
```php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/security.php';
```
- **`db.php`**: Létrehozza az adatbázis-kapcsolatot (PDO).
- **`session.php`**: Elindítja a sessiont, beállítja az időkorlátokat, és ellenőrzi, hogy a felhasználó be van-e jelentkezve.
- **`security.php`**: Jogosultság-ellenőrzés, pl. csak admin férhet hozzá bizonyos végpontokhoz.

---

### 5. **Segédfüggvények betöltése**
```php
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/lego_helpers.php';
```
- **`response.php`**: Egységes JSON válaszformátum (`success`, `message`, `data`).
- **`validation.php`**: Bemeneti adatok ellenőrzése (pl. email, jelszó, captcha).
- **`lego_helpers.php`**: Statikus LEGO-adatok lekérdezése (pl. színek, témák, alkatrészek).

---

 Összefoglalás: 

> „Az `init.php` a backend minden végpontjának kötelező induló fájlja. Ez biztosítja az alapbeállításokat, a CORS-kezelést, az adatbázis-kapcsolatot, a sessionkezelést, a biztonsági ellenőrzéseket, és betölti az összes közös segédfüggvényt. Így minden végpont egységesen, biztonságosan és megbízhatóan működik, anélkül, hogy minden fájlban újra le kellene írni ezeket.”

---