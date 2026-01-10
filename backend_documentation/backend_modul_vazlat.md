<!-- 


###################################################################################################################
################################################ CORE MODUL #######################################################
###################################################################################################################



---

##  Core modul elemei

### 1. `security.php`
- **Szerepe:** biztonsági segédfüggvények.
- **Fő funkciók:**
  - `sanitizeInput($data)` → megtisztítja a bejövő adatokat HTML/SQL injection ellen (trim + htmlspecialchars).
  - `securityError($message)` → egységes JSON hibaválasz biztonsági problémák esetén (HTTP 403).
  - `requireAdmin()` → ellenőrzi, hogy a sessionben van‑e admin jogosultság (`is_admin = true`).
  - `validateToken($token)` → CSRF vagy API token ellenőrzése session alapján.
- **Előnyök:**
  - Egységes biztonsági ellenőrzés minden modulban.
  - JSON formátumú hibaválasz → frontend könnyen kezelhető.
  - Megakadályozza a jogosulatlan hozzáférést (admin funkciók, token alapú védelem).

---

### 2. `session.php`
- **Szerepe:** egységes session kezelés.
- **Fő funkciók:**
  - Session indítás biztonságosan (`session_start()` csak egyszer).
  - `isLoggedIn()` → ellenőrzi, hogy van‑e bejelentkezett user.
  - `getCurrentUser()` → visszaadja a bejelentkezett user adatait (`user_id`, `username`, `email`).
  - `setUserSession($userId, $username, $email)` → login után beállítja a session adatokat.
  - `destroySession()` → logout, session törlése + cookie érvénytelenítés.
- **Előnyök:**
  - Egységes session logika minden modulban.
  - Könnyen hivatkozható → pl. `auth/login.php`, `auth/logout.php`, `users/get_user.php`.
  - Biztonságos kiléptetés (cookie törlés, session_destroy).

---

## Összegzés

A **core modul** biztosítja a backend alapvető működését:
- **Session kezelés** → felhasználói állapot nyomon követése.
- **Biztonsági ellenőrzések** → input tisztítás, jogosultság, token validáció.
- Ezeket az `init.php` automatikusan behúzza minden végpont elején, így minden API hívás **egységesen, biztonságosan** indul.

---


#####################################################################################################################
################################################ SHARED MODUL #######################################################
#####################################################################################################################


---

##  Shared modul elemei

### 1. `init.php`
- **Szerepe:** központi inicializáló.
- **Funkciók:**
  - Beállítja a JSON válasz formátumot.
  - Betölti az adatbázis kapcsolatot (`db.php`).
  - Behúzza a core modulokat (`session.php`, `security.php`).
  - Behúzza a shared modulokat (`response.php`, `validation.php`, `lego_helpers.php`).
- **Használat:** minden endpoint elején elég egyetlen `require_once __DIR__ . '/../shared/init.php';`.

---

### 2. `lego_helpers.php`
- **Szerepe:** LEGO metaadatok lekérése.
- **Fő függvények:**
  - `getLegoData($pdo, $item_type, $item_id)` → egyetlen elem adatai (`set`, `part`, `minifig`).
  - `getMultipleLegoData($pdo, $item_type, $item_ids)` → több elem adatai egyszerre.
- **Előnyök:**
  - Egységes SQL lekérdezés.
  - Biztonságos (prepared statement).
  - Könnyen bővíthető új típusokkal (pl. `themes`, `inventories`).

---

### 3. `response.php`
- **Szerepe:** egységes JSON válasz formátum.
- **Fő függvények:**
  - `successResponse($message, $data)` → sikeres válasz, HTTP 200.
  - `errorResponse($message, $data, $code)` → hibás válasz, HTTP kód paraméterezhető.
- **Előnyök:**
  - Minden modul ugyanazt a formátumot használja.
  - Frontend egyszerűen tudja kezelni a `status`, `message`, `data` mezőket.

---

### 4. `validation.php`
- **Szerepe:** egységes input validáció.
- **Fő függvények:**
  - `validateEmail()` → email formátum.
  - `validatePassword()` → jelszó erősség.
  - `validateRequiredFields()` → kötelező mezők megléte.
  - `validateCaptcha()` → captcha ellenőrzés.
  - `validatePhone()` → telefonszám ellenőrzés.
  - `validateAddress()` → lakcím ellenőrzés.
- **Előnyök:**
  - Minden modulban ugyanazt a logikát használjuk.
  - Könnyen bővíthető új validációs szabályokkal.

---

##  Összegzés

A **shared modul** tehát:
- Központi inicializálást (`init.php`).
- Egységes adatlekérést (`lego_helpers.php`).
- Egységes válaszformátumot (`response.php`).
- Egységes validációt (`validation.php`).

Ez a modul biztosítja, hogy minden végpont **ugyanarra az alapra épüljön**, így a refaktorálás célja teljesül: tiszta, karbantartható, egységes backend.






###################################################################################################################
################################################ AUTH MODUL #######################################################
###################################################################################################################


##  Modul vázlat sablon – Auth modul



### Modul célja
- Felhasználói regisztráció, aktiválás és bejelentkezés kezelése.  
- Session alapú autentikáció biztosítása.  
- Biztonságos jelszókezelés (bcrypt hash).  
- Egységes JSON válasz formátum minden végpontnál.  

---

### Végpontok

#### 1. register.php
- **Leírás**: Új felhasználó regisztrációja inaktív státusszal.  
- **Metódus**: POST  
- **Jogosultság**: Nyilvános.  
- **Fő logika**:  
  - Új user létrehozása `is_active = 0` státusszal.  
  - Aktiváló token generálása és mentése.  
  - Email kiküldése a verify linkkel.  
- **Hibakezelés**:  
  - 422 – hiányzó vagy hibás mezők  
  - 500 – adatbázis hiba  

---

#### 2. verify.php
- **Leírás**: Aktiváló token ellenőrzése és fiók aktiválása.  
- **Metódus**: GET  
- **Jogosultság**: Nyilvános (emailben kapott link).  
- **Fő logika**:  
  - Token ellenőrzése.  
  - Ha érvényes és inaktív, akkor `is_active = 1`, token törlése.  
- **Hibakezelés**:  
  - 400 – hiányzó vagy érvénytelen token  
  - 500 – adatbázis hiba  

---

#### 3. login.php
- **Leírás**: Felhasználó bejelentkezése.  
- **Metódus**: POST  
- **Jogosultság**: Nyilvános.  
- **Fő logika**:  
  - Email + jelszó ellenőrzése.  
  - Csak aktív fiókkal lehet belépni.  
  - Session létrehozása `$_SESSION['user_id']` értékkel.  
- **Hibakezelés**:  
  - 401 – hibás email/jelszó vagy inaktív fiók  
  - 422 – hiányzó mezők  
  - 500 – adatbázis hiba  

---

#### 4. logout.php
- **Leírás**: Felhasználó kijelentkezése.  
- **Metódus**: POST  
- **Jogosultság**: Bejelentkezett user.  
- **Fő logika**:  
  - Session törlése.  
  - Sikeres válasz visszaadása.  
- **Hibakezelés**:  
  - 401 – nincs bejelentkezés  

---

### Biztonsági megjegyzések
- Jelszó mindig bcrypt hash formában tárolódik.  
- Aktiváló token egyszer használható, utána törlődik.  
- Session alapú autentikáció → minden további modul erre épül.  
- Egységes hibakódok: 400, 401, 405, 422, 500.  

---

### Példa folyamat
1. **register.php** → új user létrehozása, aktiváló token generálása.  
2. **verify.php** → emailben kapott linkkel aktiválás.  
3. **login.php** → bejelentkezés aktív fiókkal.  
4. **logout.php** → kijelentkezés, session törlése.  

---

## Összegzés
- Az Auth modul biztosítja a teljes belépési folyamatot: regisztráció → aktiválás → login → logout.  
- Ez az alapja minden további modulnak (Users, Listings, Cart, Orders, Ratings, Admin).  
- Vizsgán és dokumentációban is jól bemutatható, mert logikusan építi fel a rendszer működését.


-------------------------------------------------------------------


---

##  Auth modul – jelenlegi állapot

- **`login.php`**
  - POST metódus.
  - JSON body: `email_or_username`, `password`.
  - Ellenőrzések: létező user, aktív státusz, jelszó hash ellenőrzés.
  - Session beállítás: `user_id`, `username`.
  - Válasz: egységes JSON (`successResponse` / `errorResponse`).

- **`logout.php`**
  - POST metódus.
  - Ellenőrzés: van‑e aktív session.
  - Ha van → session törlés (`session_unset`, `session_destroy`).
  - Válasz: egységes JSON.

- **`register.php`**
  - POST metódus.
  - JSON body: `username`, `email`, `password`, `captcha`.
  - Validáció: kötelező mezők, email formátum, tiltólista, captcha.
  - Ellenőrzés: duplikált user.
  - Új user létrehozása inaktív státusszal, `verify_token` generálás.
  - Aktiváló link visszaadása (csak fejlesztéshez).
  - Válasz: `201 Created`, egységes JSON.

- **`verify.php`**
  - GET metódus.
  - Paraméter: `token`.
  - Ellenőrzés: token létezik és inaktív userhez tartozik.
  - Aktiválás: `is_active = 1`, token törlése.
  - Válasz: egységes JSON.

---

##  Összegzés

Az **auth modul** teljesen lefedi a regisztráció → aktiválás → bejelentkezés → kijelentkezés folyamatot.  
Minden végpont az `init.php`‑t használja, így egységesen kezeli:
- adatbázis kapcsolatot,
- sessiont,
- biztonságot,
- validációt,
- JSON válaszformátumot.



---


###################################################################################################################
################################################ USERS MODUL ######################################################
###################################################################################################################

Modul vázlat sablon – Users modul

```

### Modul célja
- A bejelentkezett felhasználó profiladatainak kezelése.  
- Biztonságos hozzáférés session alapján.  
- Csak a saját adatok módosíthatók.  
- Nem ad vissza érzékeny adatokat (pl. jelszó hash).  

---

### Végpontok

#### 1. get_user.php
- **Leírás**: A bejelentkezett felhasználó adatait adja vissza.  
- **Metódus**: GET  
- **Jogosultság**: Bejelentkezett user (session szükséges).  
- **Visszaadott mezők**: `id`, `username`, `email`, `created_at`  
- **Hibakezelés**:  
  - 401 – nincs bejelentkezés  
  - 404 – felhasználó nem található  
  - 405 – érvénytelen metódus  
  - 500 – adatbázis hiba  

---

#### 2. update_user.php
- **Leírás**: A bejelentkezett felhasználó adatait frissíti.  
- **Metódus**: PUT / PATCH  
- **Jogosultság**: Bejelentkezett user (session szükséges).  
- **Frissíthető mezők**:  
  - `email` (validáció: `validateEmail`)  
  - `username`  
  - `password` (bcrypt hash, validáció: `validatePassword`)  
  - `address` (validáció: `validateAddress`)  
  - `phone` (validáció: `validatePhone`)  
- **Hibakezelés**:  
  - 401 – nincs bejelentkezés  
  - 405 – érvénytelen metódus  
  - 422 – nincs frissíthető mező vagy hibás formátum  
  - 500 – adatbázis hiba  
- **Válasz**:  
  - `status` → success/error  
  - `message` → információ a műveletről  
  - `data.updated_fields` → a frissített mezők listája  

---

### Biztonsági megjegyzések
- Session alapú hozzáférés → csak saját adatok módosíthatók.  
- Jelszó mindig hash‑elve tárolódik.  
- Validáció minden kritikus mezőnél (email, password, phone, address).  
- Egységes JSON válasz formátum → frontend könnyen kezelheti.  

---

### Példa folyamat
1. **Login** → session létrejön.  
2. **get_user.php** → felhasználó adatai lekérhetők.  
3. **update_user.php** → felhasználó módosítja emailt, címet, telefonszámot.  
4. **get_user.php** → ellenőrzés, hogy az új adatok tényleg frissültek.  

---

## Összegzés
- A Users modul két fő végpontja (get_user, update_user) teljesen refaktorálva, validálva és dokumentálva van.  
- A modul vázlat sablon bemutatja a célokat, végpontokat, hibakezelést, biztonsági megjegyzéseket és a tipikus folyamatot.  
- Vizsgán és dokumentációban is jól használható, mert rövid, áttekinthető, és minden lényeges pontot tartalmaz.

----------------------------------------------------



---

##  Users modul – jelenlegi állapot

### **`get_user.php`**
- **Szerepe:** a bejelentkezett felhasználó adatait adja vissza.
- **Metódus:** `GET`.
- **Ellenőrzések:**
  - Csak bejelentkezett user hívhatja meg (`$_SESSION['user_id']`).
  - Ha nincs session → `401 Unauthorized`.
- **Lekérdezés:** `users` táblából `id, username, email, created_at`.
- **Válasz:**
  - Siker esetén: JSON `successResponse("Felhasználói adatok betöltve", $user)`.
  - Hibák: `errorResponse()` (405, 401, 404, 500).
- **Megjegyzés:** nem ad vissza érzékeny adatokat (pl. jelszó hash).

---

### **`update_user.php`**
- **Szerepe:** a bejelentkezett felhasználó adatait frissíti.
- **Metódus:** `PUT` vagy `PATCH`.
- **Ellenőrzések:**
  - Csak bejelentkezett user hívhatja meg.
  - JSON body kötelező.
- **Frissíthető mezők:**
  - `email` → validáció (`validateEmail()`).
  - `username`.
  - `password` → validáció (`validatePassword()`), bcrypt hash.
  - `address` → validáció (`validateAddress()`).
  - `phone` → validáció (`validatePhone()`).
- **SQL:** dinamikusan összeállított `UPDATE users SET ... WHERE id = ?`.
- **Válasz:**
  - Siker esetén: JSON `successResponse("Felhasználói adatok frissítve", ["updated_fields" => array_keys($input)])`.
  - Hibák: `errorResponse()` (405, 401, 422, 500).

---

##  Összegzés

A **users modul** jelenleg két fő funkciót biztosít:
- **Lekérdezés:** bejelentkezett user adatai (`get_user.php`).
- **Frissítés:** bejelentkezett user adatai (`update_user.php`).

Mindkét végpont:
- Az `init.php`‑t használja → egységes DB, session, security, response, validation.
- Egységes JSON válaszokat ad.
- Vizsgán jól bemutatható: tiszta, moduláris, biztonságos.

---







###################################################################################################################
################################################ LISTINGS MODUL ###################################################
###################################################################################################################


### Listings modul összefoglaló

---

### Modul célja  
A **Listings modul** a Legora piactér hirdetéskezelését valósítja meg.  
- Teljes CRUD műveleteket biztosít: létrehozás, módosítás, törlés, visszaállítás, valamint listázás.  
- Minden végpont **REST-szabványos** HTTP metódusokat használ (`GET`, `POST`, `PUT/PATCH`, `DELETE`).  
- A működés **session alapú jogosultság-ellenőrzésre** épül: csak bejelentkezett felhasználók kezelhetik a saját hirdetéseiket, admin pedig speciális jogosultságokkal rendelkezik.  
- Az adatok **logikai törléssel** kezelhetők, így a hirdetések visszaállíthatók.  
- Egységes JSON válaszformátumot és hibakezelést alkalmaz, így a frontend mindig kiszámítható választ kap.  

---

### Végpontok áttekintése

| Végpont               | Metódus   | Funkció                       | Jogosultság         | Megjegyzés |
|---------              |---------  |---------                      |-------------        |------------|
| `get_listings.php`    | GET       | Hirdetések listázása          | Bejelentkezett user | Szűrés és lapozás támogatott |
| `create_listing.php`  | POST      | Új hirdetés létrehozása       | Bejelentkezett user | Validáció: item_type, item_id, quantity, price, condition |
| `update_listing.php`  | PUT/PATCH | Meglévő hirdetés módosítása   | Saját hirdetés      | Dinamikus SQL, csak megadott mezők frissülnek |
| `delete_listing.php`  | DELETE    | Hirdetés logikai törlése      | Saját hirdetés      | `deleted_at` mező kitöltése |
| `restore_listing.php` | PUT/PATCH | Törölt hirdetés visszaállítása | Saját hirdetés vagy admin | `deleted_at` mező NULL-ra állítása |

---

### Biztonsági megoldások
- **Session alapú jogosultság**: minden művelethez bejelentkezés szükséges.  
- **Tulajdonjog ellenőrzés**: csak a saját hirdetés kezelhető, kivéve admin.  
- **Logikai törlés**: a hirdetés nem törlődik fizikailag, visszaállítható.  
- **Paraméterezett SQL**: védelem SQL injection ellen.  
- **Részletes hibakódok**: `401`, `403`, `404`, `405`, `409`, `422`, `500`.  

---

### Vizsgán kiemelhető pontok
- **REST API szemlélet**: minden CRUD művelethez megfelelő HTTP metódus.  
- **Egységes JSON válaszok**: frontend fejlesztők számára kiszámítható interfész.  
- **Hibakezelés**: minden lehetséges hiba külön státuszkóddal és üzenettel.  
- **Biztonság**: session, jogosultság, logikai törlés, admin szerepkör.  
- **Modularitás**: minden végpont külön fájlban, tiszta felelősségi körrel.  

---

##  Összegzés  
A Listings modul egy **teljes, biztonságos és REST-szabványos hirdetéskezelő rendszer**, amely lefedi a hirdetések teljes életciklusát:  
- **Létrehozás → Módosítás → Törlés → Visszaállítás → Listázás**.  
Egységes dokumentációval és tesztforgatókönyvekkel rendelkezik, így a frontend fejlesztők és vizsgabizottság számára is jól bemutatható.  

---



#####################################################################################################################
################################################ ORDERS MODUL #######################################################
#####################################################################################################################


###  Orders modul összefoglaló

---

### Modul célja  
Az **Orders modul** a Legora piactér rendeléskezelését valósítja meg.  
- Lefedi a rendelés teljes életciklusát: létrehozás, listázás, részletek megtekintése, státuszváltás.  
- Biztonságos, tranzakcióalapú működést biztosít.  
- Minden végpont **REST-szabványos** HTTP metódusokat használ (`POST`, `GET`, `PUT/PATCH`).  
- Egységes JSON válaszformátumot és hibakezelést alkalmaz, így a frontend mindig kiszámítható választ kap.  

---

### Végpontok áttekintése

| Végpont              | Metódus | Funkció                        | Jogosultság | Megjegyzés |
|----------------------|---------|--------------------------------|-------------|------------|
| `checkout.php`       | POST    | Kosárból rendelés létrehozása  | Bejelentkezett user | Több eladó → több rendelés, tranzakció |
| `get_orders.php`     | GET     | Saját rendeléseinek listázása  | Bejelentkezett user | JOIN az eladó nevével, időrendi sorrend |
| `get_order.php`      | GET     | Egy rendelés részletei         | Buyer vagy Seller | Tételek + státusztörténet |
| `update_status.php`  | PUT/PATCH | Rendelés státuszának frissítése | Buyer vagy Seller | Csak engedélyezett váltások |

---

### Biztonsági megoldások
- **Session alapú jogosultság**: minden művelethez bejelentkezés szükséges.  
- **Tulajdonjog ellenőrzés**: csak a rendelésben érintett buyer vagy seller férhet hozzá.  
- **Tranzakciókezelés**: minden kritikus művelet rollback-elhető.  
- **Paraméterezett SQL**: védelem SQL injection ellen.  
- **Részletes hibakódok**: `401`, `403`, `404`, `405`, `500`.  

---

### Vizsgán kiemelhető pontok

#### 1. **checkout.php**  
- Kosárból rendelés létrehozása.  
- Több eladó → több rendelés.  
- Készletellenőrzés, rollback hiba esetén.  
- Naplózás az `order_status_history` táblába.  

#### 2. **get_orders.php**  
- A bejelentkezett user összes rendelését listázza.  
- JOIN az eladó nevével → felhasználóbarát megjelenítés.  
- Időrendi sorrend → legfrissebb rendelés elöl.  

#### 3. **get_order.php**  
- Egy adott rendelés részleteinek lekérdezése.  
- Jogosultság ellenőrzés: csak buyer vagy seller láthatja.  
- Lekéri a rendelés tételeit (`order_items`) és a státusztörténetet (`order_status_history`).  
- Így a felhasználó teljes képet kap a rendelésről.  

#### 4. **update_status.php**  
- Csak engedélyezett státuszváltások (pl. pending→paid, paid→shipped).  
- Buyer és seller szerepkörök elkülönítése.  
- Naplózás minden váltásnál.  

---

###  Vizsgán előadva – Miért jó, hogy a `get_orders.php` és a `get_order.php` külön fájlban van?

> „Azért választottuk szét a két végpontot, mert **más a céljuk és más a felhasználói élmény**.  
> - A `get_orders.php` egy **átfogó lista**: gyorsan megmutatja a felhasználónak az összes rendelését, rövid alapadatokkal (id, státusz, ár, eladó neve). Ez olyan, mint egy rendelés-áttekintő oldal.  
> - A `get_order.php` viszont egy **részletes nézet**: egyetlen rendelés minden adatát megmutatja, beleértve a tételeket és a státusztörténetet. Ez olyan, mint amikor a felhasználó rákattint egy rendelésre, és látja a teljes részleteket.  
>  
> Ha egy fájlban lenne a kettő, akkor a kód bonyolultabb, nehezebben karbantartható lenne, és a frontend sem tudná külön kezelni a listanézetet és a részletes nézetet. Így viszont tiszta a felelősségi kör:  
> - **get_orders.php → listanézet**  
> - **get_order.php → részletes nézet**  
>  
> Ez a szétválasztás a REST API egyik alapelve: minden végpontnak legyen egyértelmű, jól körülhatárolt feladata.”  

---

##  Összegzés  
Az **Orders modul** egy teljes, biztonságos és REST-szabványos rendeléskezelő rendszer, amely lefedi a rendelés teljes életciklusát:  
**Kosár → Rendelés létrehozás → Rendelések listázása → Rendelés részletei → Státuszváltás.**  

Egységes dokumentációval és tesztforgatókönyvekkel rendelkezik, így a frontend fejlesztők és vizsgabizottság számára is jól bemutatható.  

---






#####################################################################################################################
################################################## CART MODUL #######################################################
#####################################################################################################################


###  Cart modul összefoglaló

---

### Modul célja  
A **Cart modul** a Legora piactér kosárkezelését valósítja meg.  
- Lefedi a kosár teljes életciklusát: tétel hozzáadása, kosár lekérése, tétel eltávolítása.  
- Biztonságos, session‑alapú működést biztosít.  
- Minden végpont **REST-szabványos** HTTP metódusokat használ (`POST`, `GET`, `DELETE`).  
- Egységes JSON válaszformátumot és hibakezelést alkalmaz, így a frontend mindig kiszámítható választ kap.  

---

### Végpontok áttekintése

| Végpont                | Metódus | Funkció                          | Jogosultság        | Megjegyzés |
|------------------------|---------|----------------------------------|--------------------|------------|
| `add_to_cart.php`      | POST    | Új tétel hozzáadása a kosárhoz   | Bejelentkezett user | Ha már van → mennyiség növelése |
| `get_cart.php`         | GET     | Kosár tartalmának lekérése       | Bejelentkezett user | JOIN a listings + LEGO metaadat |
| `remove_from_cart.php` | DELETE  | Tétel mennyiségének csökkentése vagy teljes törlés | Bejelentkezett user | Feltételes logika: csökkentés vagy törlés |

---

### Biztonsági megoldások
- **Session alapú jogosultság**: minden művelethez bejelentkezés szükséges.  
- **Paraméterezett SQL**: védelem SQL injection ellen.  
- **Részletes hibakódok**: `401`, `405`, `422`, `404`, `500`.  
- **Egységes válaszformátum**: minden végpont ugyanazt a JSON struktúrát adja vissza.  

---

### Vizsgán kiemelhető pontok

#### 1. **add_to_cart.php**  
- Új tétel hozzáadása vagy meglévő mennyiség növelése.  
- Ellenőrzi, hogy a hirdetés létezik és nincs törölve.  
- Visszaadja a `cart_item_id`‑t és az aktuális mennyiséget.  

#### 2. **get_cart.php**  
- A bejelentkezett user kosarának teljes tartalmát listázza.  
- JOIN a listings és users táblára → minden tételhez megkapjuk a hirdetés adatait és az eladó nevét.  
- LEGO metaadatok csatolása (`lego_helpers.php`).  
- Számolja a sorösszeget (`line_total`) és a teljes összeget (`subtotal`).  

#### 3. **remove_from_cart.php**  
- Feltételes logika:  
  - Ha a meglévő mennyiség nagyobb → csökkentés.  
  - Ha kisebb vagy egyenlő → teljes törlés.  
- Visszaadja a `cart_item_id`‑t és az új mennyiséget (vagy törlés esetén csak az ID‑t).  

---

###  Vizsgán előadva – Miért jó, hogy a három funkció külön fájlban van?

> „Azért választottuk szét a kosár funkcióit három külön végpontba, mert **más a céljuk és más a felhasználói élmény**.  
> - Az `add_to_cart.php` egy **műveleti végpont**: új tételt ad hozzá vagy növeli a mennyiséget.  
> - A `get_cart.php` egy **lekérdező végpont**: listázza a kosár tartalmát, részletes adatokkal és összegzéssel.  
> - A `remove_from_cart.php` egy **módosító végpont**: csökkenti vagy törli a kosárból a tételt.  
>  
> Ha mindezt egy fájlban kezelnénk, a kód bonyolultabb, nehezebben karbantartható lenne, és a frontend sem tudná külön kezelni a hozzáadás, lekérés és törlés logikáját. Így viszont tiszta a felelősségi kör:  
> - **add_to_cart.php → hozzáadás**  
> - **get_cart.php → lekérés**  
> - **remove_from_cart.php → törlés/csökkentés**  
>  
> Ez a szétválasztás a REST API egyik alapelve: minden végpontnak legyen egyértelmű, jól körülhatárolt feladata.”  

---

##  Összegzés  
A **Cart modul** egy teljes, biztonságos és REST-szabványos kosárkezelő rendszer, amely lefedi a kosár teljes életciklusát:  
**Listings → Cart (add_to_cart, get_cart, remove_from_cart) → Orders.**  

Egységes dokumentációval és tesztforgatókönyvekkel rendelkezik, így a frontend fejlesztők és vizsgabizottság számára is jól bemutatható.  

---




######################################################################################################################
################################################### RATINGS MODUL ####################################################
######################################################################################################################


### 📚 Ratings modul összefoglaló

---

### Modul célja  
A **Ratings modul** a Legora piactér értékelési rendszerét valósítja meg.  
- Lefedi az értékelések teljes életciklusát: új értékelés hozzáadása vagy meglévő frissítése, valamint értékelések lekérése.  
- Biztonságos, session‑alapú működést biztosít.  
- REST-szabványos HTTP metódusokat használ (`POST`, `GET`).  
- Egységes JSON válaszformátumot és hibakezelést alkalmaz, így a frontend mindig kiszámítható választ kap.  

---

### Végpontok áttekintése

| Végpont               | Metódus | Funkció                                         | Jogosultság         | Megjegyzés |
|---------------------- |---------|----------------------------------               |-------------------- |------------|
| `add_rating.php`      | POST    | Új értékelés hozzáadása vagy meglévő frissítése | Bejelentkezett user | Csak akkor engedélyezett, ha volt completed rendelés |
| `get_ratings.php`     | GET     | Egy adott felhasználó értékeléseinek lekérése   | Nyilvános           | Átlagos értékelés számítása |

---

### Biztonsági megoldások
- **Session alapú jogosultság**: értékelést csak bejelentkezett user adhat.  
- **Jogosultság ellenőrzés**: csak akkor engedélyezett értékelés, ha tényleges vásárlás történt (`completed` rendelés).  
- **Önvédelem**: saját magát senki nem értékelheti.  
- **Paraméterezett SQL**: védelem SQL injection ellen.  
- **Részletes hibakódok**: `401`, `403`, `405`, `422`, `500`.  
- **Egységes válaszformátum**: minden végpont ugyanazt a JSON struktúrát adja vissza.  

---

### Vizsgán kiemelhető pontok

#### 1. **add_rating.php**  
- Új értékelés hozzáadása vagy meglévő frissítése.  
- Validáció: `rated_user_id` kötelező, `rating` 1–5 közötti egész szám.  
- Jogosultság: csak akkor engedélyezett, ha volt completed rendelés a két fél között.  
- Önvédelem: saját magát nem értékelheti senki.  
- Duplikáció kezelése: meglévő értékelés frissítése.  
- Válasz: visszaadja az értékelés azonosítóját, értékét és kommentet.  

#### 2. **get_ratings.php**  
- Egy adott felhasználóhoz tartozó értékelések listázása.  
- JOIN a `users` táblára → minden értékeléshez megjelenik az értékelő felhasználóneve.  
- Átlagos értékelés kiszámítása két tizedesre kerekítve.  
- Válasz: tartalmazza a `rated_user_id`‑t, az átlagot, az értékelések számát és a részletes listát.  

---

###  Vizsgán előadva – Miért jó, hogy az értékelések kezelése két külön fájlban van?

> „Azért választottuk szét az értékelések kezelését két külön végpontba, mert **más a céljuk és más a felhasználói élmény**.  
> - Az `add_rating.php` egy **műveleti végpont**: új értékelést ad hozzá vagy frissíti a meglévőt.  
> - A `get_ratings.php` egy **lekérdező végpont**: listázza az adott felhasználóhoz tartozó értékeléseket, és kiszámolja az átlagot.  
>  
> Ha mindezt egy fájlban kezelnénk, a kód bonyolultabb, nehezebben karbantartható lenne, és a frontend sem tudná külön kezelni az értékelés hozzáadását és lekérését. Így viszont tiszta a felelősségi kör:  
> - **add_rating.php → értékelés hozzáadása/frissítése**  
> - **get_ratings.php → értékelések lekérése és átlag számítása**  
>  
> Ez a szétválasztás a REST API egyik alapelve: minden végpontnak legyen egyértelmű, jól körülhatárolt feladata.”  

---

##  Összegzés  
A **Ratings modul** egy teljes, biztonságos és REST-szabványos értékelési rendszer, amely lefedi az értékelések teljes életciklusát:  
**Listings → Cart → Orders → Ratings (add_rating, get_ratings).**  

Egységes dokumentációval és tesztforgatókönyvekkel rendelkezik, így a frontend fejlesztők és vizsgabizottság számára is jól bemutatható.  

---



######################################################################################################################
################################################## ADMIN MODUL #######################################################
######################################################################################################################


###  Admin modul – áttekintés

Nagyon jó, hogy most az **Admin modulhoz** érkeztünk, mert ez zárja le a rendszer funkcionális körét. Ez a modul biztosítja az **adminisztrátori jogosultságokat**, vagyis a piactér működésének felügyeletét és karbantartását.  

---

### Modul célja  
Az Admin modul feladata, hogy az adminisztrátorok:  
- kezeljék a felhasználókat (törlés, visszaállítás, tiltás/engedélyezés, részletes adatok lekérése),  
- kezeljék a hirdetéseket (törlés, visszaállítás, listázás),  
- statisztikákat gyűjtsenek és jelenítsenek meg (felhasználói aktivitás, hirdetések, rendelések),  
- biztonságosan be- és kijelentkezzenek.  

---

### Fájlok áttekintése

| Fájl                         | Funkció | Megjegyzés |
|-------------------------------|---------|------------|
| `admin_login.php`             | Admin bejelentkezés | Session alapú autentikáció |
| `logout.php`                  | Admin kijelentkezés | Session törlése |
| `admin_get_user_list.php`     | Felhasználók listázása | Aktív felhasználók |
| `get_users.php`               | Felhasználók listázása | Általános user lista |
| `get_user_details.php`        | Egy felhasználó részletes adatai | Profil + aktivitás |
| `admin_delete_user.php`       | Felhasználó törlése | Soft delete (flag) |
| `admin_restore_user.php`      | Felhasználó visszaállítása | Soft delete visszavonása |
| `toggle_user.php`             | Felhasználó tiltása/engedélyezése | Aktív státusz váltás |
| `admin_get_listings_list.php` | Hirdetések listázása | Aktív hirdetések |
| `get_deleted_listings.php`    | Törölt hirdetések listázása | Soft delete után |
| `admin_delete_listing.php`    | Hirdetés törlése | Soft delete |
| `admin_restore_listing.php`   | Hirdetés visszaállítása | Soft delete visszavonása |
| `delete_listing.php`          | Hirdetés törlése (nem admin) | User saját hirdetés törlése |
| `restore_listing.php`         | Hirdetés visszaállítása (nem admin) | User saját hirdetés visszaállítása |
| `get_stats.php`               | Statisztikák lekérése | Egy adott userhez vagy hirdetéshez |
| `get_all_stats.php`           | Globális statisztikák | Rendszerszintű összesítés |

---

### Biztonsági megoldások
- **Session alapú autentikáció**: csak bejelentkezett admin férhet hozzá.  
- **Jogosultság ellenőrzés**: minden admin funkció külön ellenőrzi, hogy az aktuális user admin‑e.  
- **Soft delete**: törlésnél nem végleges törlés történik, hanem `deleted_at` mező beállítása → visszaállítható.  
- **Egységes JSON válaszformátum**: minden admin végpont ugyanazt a struktúrát adja vissza.  
- **Statisztikai funkciók**: segítik a rendszer átláthatóságát és vizsgán jól bemutathatóak.  

---

###  Vizsgán előadva – Miért jó, hogy az admin modul külön van?

> „Az admin modul különválasztása azért fontos, mert **más jogosultsági szintet és más funkciókat** kezel, mint a normál user modulok.  
> - Az admin funkciók közvetlenül befolyásolják a rendszer működését (felhasználók, hirdetések, statisztikák).  
> - Ha ezek keverednének a normál user funkciókkal, a kód bonyolultabb és kevésbé biztonságos lenne.  
> - Így viszont tiszta a felelősségi kör: a user modul a piactér használatát, az admin modul pedig a piactér felügyeletét biztosítja.  
> Ez a szétválasztás a **biztonság és karbantarthatóság** alapelve.”  

---

##  Összegzés  
Az **Admin modul** a Legora piactér „háttérirányítója”:  
- Felhasználók és hirdetések kezelése (törlés, visszaállítás, tiltás).  
- Statisztikák gyűjtése és megjelenítése.  
- Biztonságos be- és kijelentkezés.  

Egységes dokumentációval és tesztforgatókönyvekkel lesz feldolgozva, így vizsgán jól bemutatható.  

---



                                            **Összegzés admin modul API végpontokról**

Most az admin modul végpontjait kell összefoglalnom. Ezek közé tartoznak a delete_listing.php, restore_listing.php, get_stats.php, get_all_stats.php, és esetleg a get_users.php. Minden végponthoz le kell írnom a célját, metódust, hitelesítést, kérés/válasz, státuszkódokat, tesztelési lefedettséget, biztonsági megfontolásokat, adatmodell mezőket, hibakezelést, valamint a tesztelési és dokumentációs irányelveket, jövőbeli fejlesztéseket is említve. Az idézeteket nem szükséges megadni.
### Admin modul összefoglaló

---

### Áttekintés  
Az admin modul célja, hogy biztonságos, visszaállítható és átlátható módon kezelje a hirdetéseket és a rendszer statisztikáit. A modul egységes JSON válaszformátumot, konzisztens HTTP státuszkódokat és soft delete megközelítést alkalmaz, így minden művelet auditálható és vizsgán jól bemutatható.

---

### Fő végpontok

#### Hirdetés törlése – delete_listing.php
- **Cél:** Soft delete bármely hirdetéshez, admin jogosultsággal.
- **Metódus:** POST
- **Auth:** Aktív admin session szükséges.
- **Kötelező paraméter:** id (a hirdetés azonosítója)
- **Fő státuszkódok:**  
  - **200:** Sikeres törlés  
  - **401:** Nincs admin session  
  - **404:** Nem található hirdetés  
  - **409:** Már törölt hirdetés  
  - **422:** Hiányzó id  
  - **405/500:** Metódus/DB hiba
- **Megjegyzés:** Soft delete: deleted_at = NOW(); a cím visszaadásával segíti a frontend visszajelzést.

#### Hirdetés visszaállítása – restore_listing.php
- **Cél:** Soft delete visszavonása, törölt hirdetés újraaktíválása.
- **Metódus:** POST
- **Auth:** Aktív admin session szükséges.
- **Kötelező paraméter:** id
- **Fő státuszkódok:**  
  - **200:** Sikeres visszaállítás  
  - **401:** Nincs admin session  
  - **404:** Nem található hirdetés  
  - **409:** Nem törölt hirdetés  
  - **422:** Hiányzó id  
  - **405/500:** Metódus/DB hiba
- **Megjegyzés:** Visszaállítás: deleted_at = NULL; konzisztens a törlési folyamattal.

#### Összesítő statisztikák – get_stats.php
- **Cél:** Gyors dashboard statisztikák (globális számok).
- **Metódus:** GET
- **Auth:** Aktív admin session szükséges.
- **Visszaadott mezők:** active_listings, deleted_listings, active_users, inactive_users, total_users
- **Fő státuszkódok:**  
  - **200:** Sikeres lekérés  
  - **401:** Nincs admin session  
  - **405/500:** Metódus/DB hiba
- **Megjegyzés:** Minden számláló integerként kerül visszaadásra.

#### Komplex statisztikák – get_all_stats.php
- **Cél:** Rendszerszintű áttekintés (globális + felhasználói + hirdetés-ár statisztika).
- **Metódus:** GET
- **Auth:** Aktív admin session szükséges.
- **Visszaadott mezők:**  
  - **global_stats:** aktiv/deleted listings, active/inactive/total users  
  - **user_stats:** id, username, email, is_active, total_listings, active_listings, deleted_listings  
  - **listing_stats:** total_listings, avg_price, min_price, max_price
- **Fő státuszkódok:**  
  - **200:** Sikeres lekérés  
  - **401:** Nincs admin session  
  - **405/500:** Metódus/DB hiba
- **Megjegyzés:** Mezőnevek konzisztens snake_case; CASE WHEN aggregációval számolt bontások.

---

### Közös irányelvek és konvenciók

- **Soft delete stratégia:**  
  - deleted_at mező használata törlésre és visszaállításra.  
  - Adatmegőrzés, auditálhatóság, visszaállíthatóság.

- **Autentikáció és jogosultság:**  
  - **Admin session:** kötelező az admin modul végpontokhoz.  
  - **Egységes ellenőrzés:** a kérések elején.

- **Válaszformátum:**  
  - **status:** success vagy error  
  - **message:** rövid, emberi olvasásra alkalmas üzenet  
  - **adatmezők:** konzisztens snake_case elnevezés, típushelyes értékek

- **HTTP státuszkódok:**  
  - **200:** sikeres művelet  
  - **401:** nincs jogosultság/session  
  - **403:** tiltott (ha szükségessé válik finomabb jogosultság)  
  - **404:** nem található erőforrás  
  - **405:** rossz metódus  
  - **409:** konfliktus (már törölt/nem törölt)  
  - **422:** hiányzó vagy hibás paraméter  
  - **500:** szerver/DB hiba

- **Biztonság:**  
  - **Input validáció:** kötelező paraméterek ellenőrzése (id).  
  - **SQL:** parametrizált lekérdezések, nincs string konkatenáció.  
  - **Session kezelés:** ellenőrzés az elején, egységes hibaüzenettel.

---

### Tesztelés és dokumentáció

- **Thunder Client forgatókönyvek:**  
  - **Pozitív esetek:** sikeres törlés/visszaállítás és statisztika lekérés.  
  - **Negatív esetek:** nincs session, rossz metódus, hiányzó id, nem létező erőforrás, konfliktus állapot, DB hiba.

- **API dokumentáció:**  
  - Minden végponthoz tartalmazza az URL-t, metódust, request/response példákat és státuszkódokat.  
  - Frontend megjegyzések: mely mezők jelenjenek meg, mely hibákat kell külön kezelni.

---

### Adatmodell és mezőnevek

- **Listings tábla:**  
  - **id:** egyedi azonosító  
  - **user_id:** tulajdonos felhasználó  
  - **title:** hirdetés címe  
  - **price:** ár (statisztikákhoz)  
  - **deleted_at:** soft delete időbélyeg

- **Users tábla:**  
  - **id, username, email, is_active**  
  - A statisztikákhoz kapcsolva: total_listings, active_listings, deleted_listings (aggregált mezők a lekérdezésben)

---

### Javasolt fejlesztések

- **Audit log:**  
  - **Label:** Műveletnapló törlés/visszaállítás műveletekhez admin azonosítóval és időbélyeggel.

- **Szűrés és pagináció a user_stats‑hoz:**  
  - **Label:** oldalszám, limit, rendezés (pl. total_listings DESC).

- **Idempotens visszaállítás/törlés visszajelzéssel:**  
  - **Label:** ha nincs változás, visszatérő üzenet legyen informatív, de ne hiba (opcionálisan 200 + “no-op”).

- **Rate limiting / CSRF védelem:**  
  - **Label:** POST végpontokhoz CSRF token, admin panelből érkező kérések védelme.

---

### Gyors ellenőrzőlista

- **Metódus ellenőrzés:** minden végpontnál a kérés elején.  
- **Session ellenőrzés:** admin végpontoknál kötelező.  
- **Paraméterek:** POST esetén id kötelező; GET esetén nincs body.  
- **Hibakódok:** használj megfelelő, konzisztens státuszkódokat.  
- **Mezőnevek:** snake_case, konzisztens az adatbázissal.  
- **JSON válasz:** status, message, adatok; típushelyes értékek.  




##################################################################################################################
##################################################################################################################
##################################################################################################################



  __      __                                                  __ 
  |  \    |  \                                                |  \
_| $$_   | $$____    ______          ______   _______    ____| $$
|   $$ \  | $$    \  /      \        /      \ |       \  /      $$
\$$$$$$  | $$$$$$$\|  $$$$$$\      |  $$$$$$\| $$$$$$$\|  $$$$$$$
  | $$ __ | $$  | $$| $$    $$      | $$    $$| $$  | $$| $$  | $$
  | $$|  \| $$  | $$| $$$$$$$$      | $$$$$$$$| $$  | $$| $$__| $$
  \$$  $$| $$  | $$ \$$     \       \$$     \| $$  | $$ \$$    $$
    \$$$$  \$$   \$$  \$$$$$$$        \$$$$$$$ \$$   \$$  \$$$$$$$
                                                                  
                                                                  
                                                                  







 -->