<!-- ##################################################################################################################
################################################ AUTH MODUL ######################################################
##################################################################################################################

----------------------------------------------- auth/login.php ---------------------------------------------------


http://localhost/legora/auth/login.php

POST /auth/login.php
- Leírás: Bejelentkezés e‑mail vagy felhasználónév + jelszó párossal. Csak aktivált fiókok engedélyezettek.
- Jogosultság: Nyilvános (bejelentkezés), csak aktív fiókoknál sikeres.

- Request body:
{
  "email_or_username": "user9@example.com",
  "password": "Test123!"
}

- Response (200 OK):
{
  "status": "success",
  "message": "Sikeres bejelentkezés",
  "data": {
    "user_id": 9,
    "username": "user9",
    "email": "user9@example.com"
  }
}

- Response (401 Unauthorized):
{
  "status": "error",
  "message": "Hibás jelszó",
  "data": null
}

- Response (403 Forbidden):
{
  "status": "error",
  "message": "A fiók nincs aktiválva. Kérlek, ellenőrizd az e‑mail fiókodat.",
  "data": null
}

- Response (401 Unauthorized – nem létező user):
{
  "status": "error",
  "message": "Hibás felhasználónév vagy e‑mail",
  "data": null
}

- Response (422 Unprocessable Entity – hiányzó mezők):
{
  "status": "error",
  "message": "Minden mező kitöltése kötelező (email_or_username, password)",
  "data": null
}

- Response (400 Bad Request – érvénytelen JSON):
{
  "status": "error",
  "message": "Érvénytelen JSON formátum",
  "data": null
}

- Response (405 Method Not Allowed – GET kérés):
{
  "status": "error",
  "message": "Érvénytelen kérés (csak POST engedélyezett)",
  "data": null
}

- Megjegyzés:
- A frontend a `status` mezőt figyeli: ha `success`, folytathatja a munkát.
- Hibák esetén a `message` mezőt jelenítse meg a felhasználónak.
- A `data` mező csak siker esetén tartalmaz adatokat.
- Session beállítás történik a háttérben, de a frontend ezt nem látja.
```

---

## Összegzés
- Az `auth/login.php` végpont dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, mit küldjenek és mit várjanak vissza.  
- Vizsgán is jól bemutatható: először a leírás, majd a konkrét JSON példák, végül a megjegyzések.

---




----------------------------------------------- auth/logout.php ---------------------------------------------------


http://localhost/legora/auth/logout.php

POST /auth/logout.php
- Leírás: Kijelentkezteti az aktuálisan bejelentkezett felhasználót.  
- Jogosultság: Bejelentkezett user (session szükséges).  
- Megjegyzés: Nincs szükség request body‑ra, csak a session cookie kell, amit a login után a böngésző tárol.

- Request body: nincs

- Response (200 OK – sikeres kijelentkezés):
{
  "status": "success",
  "message": "Sikeres kijelentkezés",
  "data": null
}

- Response (401 Unauthorized – nincs aktív bejelentkezés):
{
  "status": "error",
  "message": "Nincs aktív bejelentkezés",
  "data": null
}

- Response (405 Method Not Allowed – GET kérés):
{
  "status": "error",
  "message": "Érvénytelen kérés (csak POST engedélyezett)",
  "data": null
}

- Megjegyzés:
- A frontend a `status` mezőt figyeli: ha `success`, törölje a kliens oldali user state‑et (pl. localStorage, Redux, Vuex).  
- Hibák esetén a `message` mezőt jelenítse meg a felhasználónak.  
- A `data` mező mindig `null`, mert kijelentkezéskor nincs visszaadott adat.
```

---

## Összegzés
- Az `auth/logout.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body információt, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogy a kijelentkezésnél nincs body, csak a session cookie számít, és mit kell tenniük a kliens oldalon.  
- Vizsgán is jól bemutatható: egyszerű, tiszta, egységes.





----------------------------------------------- auth/register.php ---------------------------------------------------

-----------------------------------------
api_endpoints.md – Auth modul
-----------------------------------------

http://localhost/legora/auth/register.php

POST /auth/register.php
- Leírás: Új felhasználó regisztrációja. A fiók inaktívként jön létre, aktiválás szükséges a verify.php végponttal.
- Jogosultság: Nyilvános (regisztráció).

- Request body:
{
  "username": "tesztuser",
  "email": "tesztuser@example.com",
  "password": "Test123!",
  "captcha": "1234"
}

- Response (201 Created – sikeres regisztráció):
{
  "status": "success",
  "message": "Regisztráció sikeres. Kérlek, ellenőrizd az e‑mail fiókodat az aktiváló linkért.",
  "data": {
    "user_id": 16,
    "username": "tesztuser",
    "email": "tesztuser@example.com",
    "verify_link": "http://localhost/legora/auth/verify.php?token=XYZ" 
  }
}

- Response (409 Conflict – duplikált email/username):
{
  "status": "error",
  "message": "Ez az e‑mail vagy felhasználónév már foglalt",
  "data": null
}

- Response (422 Unprocessable Entity – hibás email formátum):
{
  "status": "error",
  "message": "Hibás email formátum",
  "data": null
}

- Response (422 Unprocessable Entity – tiltólistás email):
{
  "status": "error",
  "message": "Ez az e‑mail cím nem engedélyezett",
  "data": null
}

- Response (403 Forbidden – hibás CAPTCHA):
{
  "status": "error",
  "message": "Hibás CAPTCHA",
  "data": null
}

- Response (422 Unprocessable Entity – hiányzó mezők):
{
  "status": "error",
  "message": "Minden mező kitöltése kötelező (username, email, password, captcha)",
  "data": null
}

- Response (400 Bad Request – érvénytelen JSON):
{
  "status": "error",
  "message": "Érvénytelen JSON formátum",
  "data": null
}

- Response (405 Method Not Allowed – GET kérés):
{
  "status": "error",
  "message": "Érvénytelen kérés (csak POST engedélyezett)",
  "data": null
}

- Response (500 Internal Server Error – adatbázis hiba):
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}

- Megjegyzés:
- A frontend a `status` mezőt figyeli: ha `success`, akkor a felhasználót tájékoztatja az aktiváló e‑mailről.  
- Hibák esetén a `message` mezőt jelenítse meg a felhasználónak.  
- A `verify_link` mező csak fejlesztési célra kerül visszaadásra, éles környezetben nem.  
- A jelszó minden userhez egységesen `Test123!` a teszteléshez.
```

---

##  Összegzés
- Az `auth/register.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, mit küldjenek és mit várjanak vissza.  
- Jól bemutatható: először a leírás, majd a konkrét JSON példák, végül a megjegyzések.

---


----------------------------------------------- auth/verify.php ---------------------------------------------------

Rendben, András 🙂  
Akkor elkészítjük az **api_endpoints.md dokumentációt** az `auth/verify.php` végponthoz, a rögzített sablon szerint.

---

```markdown
-----------------------------------------
api_endpoints.md – Auth modul
-----------------------------------------

http://localhost/legora/auth/verify.php

GET /auth/verify.php?token=XYZ
- Leírás: Aktiválja a regisztrációkor létrehozott felhasználói fiókot a token ellenőrzésével.  
- Jogosultság: Nyilvános (regisztráció után e‑mailben kapott link).  
- Biztonság: A token egyszer használható, aktiválás után törlődik.

- Request paraméter:
?token=XYZ

- Response (200 OK – sikeres aktiválás):
{
  "status": "success",
  "message": "Fiók sikeresen aktiválva",
  "data": null
}

- Response (400 Bad Request – hiányzó token):
{
  "status": "error",
  "message": "Hiányzó token",
  "data": null
}

- Response (400 Bad Request – érvénytelen vagy már aktivált token):
{
  "status": "error",
  "message": "Érvénytelen vagy már aktivált token",
  "data": null
}

- Response (500 Internal Server Error – adatbázis hiba):
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}

- Megjegyzés:
- A frontend a `status` mezőt figyeli: ha `success`, akkor a felhasználót tájékoztatja, hogy a fiók aktiválva lett.  
- Hibák esetén a `message` mezőt jelenítse meg a felhasználónak.  
- A regisztrációs folyamat sorrendje:  
  1. `register.php` → új user inaktív státusszal, token generálás  
  2. `verify.php` → token ellenőrzés, fiók aktiválás  
  3. `login.php` → csak aktív fiókkal lehet belépni
```

---

## Összegzés
- Az `auth/verify.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request paramétert, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan működik az aktiválási folyamat.  
- Vizsgán is jól bemutatható: a regisztráció → aktiválás → login folyamat tisztán látszik.




###################################################################################################################
################################################ USERS MODUL ######################################################
###################################################################################################################

-----------------------------------------------users/get_user.php ------------------------------------------------

-----------------------------------------
api_endpoints.md – Users modul
-----------------------------------------

http://localhost/legora/users/get_user.php

GET /users/get_user.php
- Leírás: A bejelentkezett felhasználó adatait adja vissza.  
- Jogosultság: Bejelentkezett user (session szükséges).  
- Biztonság: Nem ad vissza érzékeny adatokat (pl. jelszó hash).

- Request body: nincs (csak session cookie szükséges)

- Response (200 OK – sikeres lekérés):
{
  "status": "success",
  "message": "Felhasználói adatok betöltve",
  "data": {
    "id": 9,
    "username": "user9",
    "email": "user9@example.com",
    "created_at": "2025-11-01 12:34:56"
  }
}

- Response (401 Unauthorized – nincs bejelentkezés):
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}

- Response (404 Not Found – felhasználó nem található):
{
  "status": "error",
  "message": "Felhasználó nem található",
  "data": null
}

- Response (405 Method Not Allowed – POST kérés):
{
  "status": "error",
  "message": "Érvénytelen kérés (csak GET engedélyezett)",
  "data": null
}

- Response (500 Internal Server Error – adatbázis hiba):
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}

- Megjegyzés:
- A frontend a `status` mezőt figyeli: ha `success`, akkor a `data` mezőben kapott user adatokat jelenítse meg.  
- Hibák esetén a `message` mezőt jelenítse meg a felhasználónak.  
- A végpont csak bejelentkezett felhasználó esetén működik, ezért a login → get_user folyamatot kell követni.
```

---

## Összegzés
- Az `users/get_user.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request paramétereket, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell lekérni a bejelentkezett user adatait.  
- Jól bemutatható: login után → get_user → adatok megjelenítése.




---------------------------------------------- users/update_user.php ------------------------------------------------

-----------------------------------------
api_endpoints.md – Users modul
-----------------------------------------

http://localhost/legora/users/update_user.php

PUT /users/update_user.php  
PATCH /users/update_user.php  
- Leírás: A bejelentkezett felhasználó adatait frissíti.  
- Jogosultság: Bejelentkezett user (session szükséges).  
- Frissíthető mezők: `email`, `username`, `password`, `address`, `phone`.  
- Biztonság:  
  - Jelszó esetén bcrypt hash készül.  
  - Email, password, address, phone validáció történik.  

- Request body (JSON példa – email + phone frissítés):
```json
{
  "email": "newmail@example.com",
  "phone": "+36 30 123 4567"
}
```

- Response (200 OK – sikeres frissítés):
```json
{
  "status": "success",
  "message": "Felhasználói adatok frissítve",
  "data": {
    "updated_fields": ["email", "phone"]
  }
}
```

- Response (401 Unauthorized – nincs bejelentkezés):
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```

- Response (422 Unprocessable Entity – nincs frissíthető mező):
```json
{
  "status": "error",
  "message": "Nincs frissíthető mező megadva",
  "data": null
}
```

- Response (422 Unprocessable Entity – hibás formátum):
```json
{
  "status": "error",
  "message": "Hibás telefonszám formátum",
  "data": null
}
```
vagy
```json
{
  "status": "error",
  "message": "Hibás lakcím formátum",
  "data": null
}
```

- Response (405 Method Not Allowed – GET kérés):
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak PUT/PATCH engedélyezett)",
  "data": null
}
```

- Response (500 Internal Server Error – adatbázis hiba):
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```

- Megjegyzés:
  - A frontend a `status` mezőt figyeli: ha `success`, akkor a `data.updated_fields` mezőben kapott listát használja a frissített mezők megjelenítésére.  
  - Hibák esetén a `message` mezőt jelenítse meg a felhasználónak.  
  - A végpont csak bejelentkezett felhasználó esetén működik, ezért a login → update_user → get_user folyamatot kell követni.
```

---

##  Összegzés
- Az `update_user.php` dokumentációja most már lefedi az új `address` és `phone` mezőket is.  
- Tartalmazza az **URL‑t, metódust, request body példákat, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell frissíteni a felhasználói adatokat.  
- Jól bemutatható: login után → update_user → get_user → ellenőrzés.








######################################################################################################################
################################################ LISTINGS MODUL ######################################################
######################################################################################################################


----------------------------------------------- listings/get_listings.php.php ------------------------------------

### api_endpoints.md – Listings modul / `get_listings.php`

---

### Cél  
A `get_listings.php` endpoint feladata, hogy a piactér hirdetéseit listázza, és a felhasználói adatok mellett automatikusan csatolja a hivatalos LEGO metaadatokat is.  
Így a frontend azonnal meg tudja jeleníteni a hirdetést a hivatalos képpel, névvel, évvel, színnel – az eladó által megadott leírásokkal kiegészítve.  

- Csak **GET** kérést enged.  
- Lapozás (`page`, `limit`) és szűrés (`item_type`, `seller_id`) támogatott.  
- Csak aktív hirdetések (`deleted_at IS NULL`).  
- LEGO metaadatok (`lego_data`) a helperen keresztül kerülnek be.  
- Egységes JSON válasz formátum.  

---

### Endpoint
`GET http://localhost/legora/listings/get_listings.php`



GET /listings/get_listings.php  
- **Leírás:** A piactér hirdetéseinek listázása. A végpont visszaadja az aktív hirdetéseket, és minden hirdetéshez automatikusan csatolja a hivatalos LEGO metaadatokat (név, év, kép, szín, alkatrészszám).  
- **Jogosultság:** Nyilvános (nem szükséges bejelentkezés).  
- **Szűrés és lapozás:**  
  - `page` *(opcionális)* → lapozás, alapértelmezett: 1  
  - `limit` *(opcionális)* → elemek száma, alapértelmezett: 20, max: 100  
  - `item_type` *(opcionális)* → szűrés típus szerint (`set`, `part`, `minifig`)  
  - `seller_id` *(opcionális)* → szűrés adott eladó hirdetéseire  

---

- **Request példa (GET – alapértelmezett lekérés):**  
```
GET http://localhost/legora/listings/get_listings.php
```

- **Response (200 OK – sikeres lekérés):**  
```json
{
  "status": "success",
  "message": "Hirdetések listázva",
  "data": {
    "listings": [
      {
        "id": 1,
        "item_type": "set",
        "item_id": "010423-1",
        "quantity": 1,
        "price": 17500,
        "item_condition": "new",
        "description": "The Majestic Horse",
        "created_at": "2025-11-21 10:00:00",
        "seller": "user18",
        "lego_data": {
          "name": "The Majestic Horse",
          "year": 2023,
          "img_url": "https://cdn.rebrickable.com/media/sets/010423-1.jpg",
          "num_parts": 492
        }
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 120
    }
  }
}
```

---

- **Response (405 Method Not Allowed – POST kérés):**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak GET engedélyezett)",
  "data": null
}
```

- **Response (500 Internal Server Error – adatbázis hiba):**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```

---

### Megjegyzés a frontend számára
- A `status` mezőt figyeljék:  
  - `success` → a `data.listings` tömb tartalmazza a hirdetéseket, a `data.pagination` objektum pedig a lapozási adatokat.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `lego_data` mező minden hirdetésnél tartalmazza a hivatalos LEGO metaadatokat, így a frontendnek nem kell külön lekérdezést végeznie.  
- A lapozás (`page`, `limit`, `total`) segít a hirdetések oldalakra bontásában.  
- Szűrési paraméterekkel a frontend könnyen tudja megjeleníteni pl. csak egy eladó hirdetéseit vagy csak `set` típusú termékeket.  

---

##  Összegzés
- Az `get_listings.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, query paramétereket, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hívni a végpontot, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: először a cél, majd a konkrét JSON példák, végül a frontend megjegyzések.  

---





----------------------------------------------- listings/create_listing.php --------------------------------------

  

-----------------------------------------  
api_endpoints.md – Listings modul  
-----------------------------------------  

### Cél  
A `create_listing.php` endpoint feladata, hogy új hirdetést hozzon létre a piactéren.  
- Csak **POST** kérést enged.  
- Csak **bejelentkezett felhasználó** adhat fel hirdetést.  
- Validálja a bemenetet (`item_type`, `item_id`, `quantity`, `price`, `item_condition`).  
- Ellenőrzi, hogy a megadott LEGO elem valóban létezik a statikus adatbázisban (`sets`, `parts`, `minifigs`).  
- Mentés után visszaadja a hirdetés adatait JSON formátumban.  
- Egységes hibakezelést és válaszformátumot használ, így a frontend mindig kiszámítható választ kap.  

---

### Endpoint  
`POST http://localhost/legora/listings/create_listing.php`  

---

### Request body (JSON példa – új hirdetés létrehozása)  
```json
{
  "item_type": "set",
  "item_id": "010423-1",
  "quantity": 1,
  "price": 17500,
  "item_condition": "new",
  "description": "The Majestic Horse"
}
```  

---

### Response példák  

**200 OK – sikeres létrehozás**  
```json
{
  "status": "success",
  "message": "Hirdetés sikeresen létrehozva",
  "data": {
    "listing_id": 25,
    "item_type": "set",
    "item_id": "010423-1",
    "quantity": 1,
    "price": 17500,
    "item_condition": "new",
    "description": "The Majestic Horse"
  }
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges a hirdetés feladásához",
  "data": null
}
```  

---

**400 Bad Request – érvénytelen JSON**  
```json
{
  "status": "error",
  "message": "Érvénytelen JSON formátum",
  "data": null
}
```  

---

**422 Unprocessable Entity – érvénytelen item_type**  
```json
{
  "status": "error",
  "message": "Érvénytelen item_type (set, part, minifig megengedett)",
  "data": null
}
```  

**422 Unprocessable Entity – érvénytelen item_condition**  
```json
{
  "status": "error",
  "message": "Érvénytelen item_condition (new, used megengedett)",
  "data": null
}
```  

**422 Unprocessable Entity – hiányzó vagy hibás adatok**  
```json
{
  "status": "error",
  "message": "Hiányzó vagy hibás adatok (item_id, quantity, price kötelező)",
  "data": null
}
```  

**422 Unprocessable Entity – nem létező LEGO elem**  
```json
{
  "status": "error",
  "message": "A megadott LEGO elem nem található az adatbázisban",
  "data": null
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak POST engedélyezett)",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` mező tartalmazza az új hirdetés adatait.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `listing_id` mező az újonnan létrehozott hirdetés azonosítója, ezt a frontend később használhatja pl. részletek megjelenítésére vagy módosításra.  
- A validációs hibák mindig `422` státuszkóddal térnek vissza, így a frontend könnyen tudja kezelni a hibás inputokat.  
- A végpont csak bejelentkezett felhasználó esetén működik, ezért a login → create_listing → get_listings folyamatot kell követni.  

---

## Összegzés  
- Az `create_listing.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetést létrehozni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: login után → create_listing → get_listings → ellenőrzés.  

---




----------------------------------------------- listings/update_listing.php------------------------------------
 
-----------------------------------------  
api_endpoints.md – Listings modul  
-----------------------------------------  

### Cél  
Az `update_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó **módosíthassa a saját hirdetését**.  
- Csak **PUT/PATCH** kérést enged.  
- Csak a **saját hirdetését** frissítheti a user.  
- Csak bizonyos mezők frissíthetők: `quantity`, `price`, `item_condition`, `description`.  
- Ellenőrzi, hogy a hirdetés létezik, nem törölt, és valóban a bejelentkezett userhez tartozik.  
- Dinamikusan építi az SQL-t, így csak a megadott mezők frissülnek.  
- Egységes hibakezelést és válaszformátumot használ.  

### Endpoint  
`PUT http://localhost/legora/listings/update_listing.php 
`PATCH http://localhost/legora/listings/update_listing.php

---

### Request body (JSON példa – ár és leírás frissítése)  
```json
{
  "listing_id": 25,
  "price": 18000,
  "description": "Frissített leírás"
}
```  

---

### Response példák  

**200 OK – sikeres frissítés**  
```json
{
  "status": "success",
  "message": "Hirdetés sikeresen frissítve",
  "data": {
    "listing_id": 25,
    "updated_fields": ["price", "description"]
  }
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges a hirdetés módosításához",
  "data": null
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak PUT/PATCH engedélyezett)",
  "data": null
}
```  

---

**422 Unprocessable Entity – érvénytelen vagy hiányzó listing_id**  
```json
{
  "status": "error",
  "message": "Érvénytelen vagy hiányzó listing_id",
  "data": null
}
```  

**422 Unprocessable Entity – nincs frissíthető mező megadva**  
```json
{
  "status": "error",
  "message": "Nincs frissíthető mező megadva",
  "data": null
}
```  

---

**404 Not Found – hirdetés nem létezik**  
```json
{
  "status": "error",
  "message": "A hirdetés nem található",
  "data": null
}
```  

---

**403 Forbidden – nem a saját hirdetés**  
```json
{
  "status": "error",
  "message": "Nincs jogosultságod ennek a hirdetésnek a módosítására",
  "data": null
}
```  

---

**409 Conflict – törölt hirdetés**  
```json
{
  "status": "error",
  "message": "A hirdetés már törölve lett, nem módosítható",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data.updated_fields` mezőben kapott listát használják a frissített mezők megjelenítésére.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `listing_id` kötelező, nélküle nem történik frissítés.  
- Csak a saját hirdetés módosítható, így a frontendnek figyelnie kell a user jogosultságára.  
- Törölt hirdetés nem frissíthető, a frontendnek ezt hibaként kell kezelnie.  

---

## Összegzés  
- Az `update_listing.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetést frissíteni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: login után → create_listing → update_listing → get_listings → ellenőrzés.  

---




----------------------------------------------- listings/delete_listing.php.php ------------------------------------
 

-----------------------------------------  
api_endpoints.md – Listings modul  
-----------------------------------------  

### Cél  
A `delete_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó **logikailag törölje a saját hirdetését**.  
- Csak **DELETE** kérést enged.  
- Csak a **saját hirdetését** törölheti a user.  
- Nem fizikai törlés történik, hanem a `deleted_at` mező kitöltése → így később visszaállítható (`restore_listing.php`).  
- Ellenőrzi, hogy a hirdetés létezik, nem törölt, és valóban a bejelentkezett userhez tartozik.  
- Egységes hibakezelést és válaszformátumot használ.  

---

### Endpoint  
`DELETE http://localhost/legora/listings/delete_listing.php`  

---

### Request body (JSON példa – hirdetés törlése)  
```json
{
  "listing_id": 25
}
```  

---

### Response példák  

**200 OK – sikeres törlés**  
```json
{
  "status": "success",
  "message": "Hirdetés sikeresen törölve (logikai törlés)",
  "data": {
    "listing_id": 25,
    "deleted_at": "2025-12-02 15:56:00"
  }
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges a hirdetés törléséhez",
  "data": null
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak DELETE engedélyezett)",
  "data": null
}
```  

---

**422 Unprocessable Entity – érvénytelen vagy hiányzó listing_id**  
```json
{
  "status": "error",
  "message": "Érvénytelen vagy hiányzó listing_id",
  "data": null
}
```  

---

**404 Not Found – hirdetés nem létezik**  
```json
{
  "status": "error",
  "message": "A hirdetés nem található",
  "data": null
}
```  

---

**403 Forbidden – nem a saját hirdetés**  
```json
{
  "status": "error",
  "message": "Nincs jogosultságod ennek a hirdetésnek a törlésére",
  "data": null
}
```  

---

**409 Conflict – már törölt hirdetés**  
```json
{
  "status": "error",
  "message": "A hirdetés már törölve lett korábban",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data.deleted_at` mező tartalmazza a törlés időpontját.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `listing_id` kötelező, nélküle nem történik törlés.  
- Csak a saját hirdetés törölhető, így a frontendnek figyelnie kell a user jogosultságára.  
- A törlés logikai, tehát a hirdetés később visszaállítható a `restore_listing.php` végponttal.  

---

##  Összegzés  
- Az `delete_listing.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetést törölni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: login után → create_listing → update_listing → delete_listing → get_listings → ellenőrzés.  

--




----------------------------------------------- listings/restore_listing.php.php ------------------------------------


### api_endpoints.md – Listings modul / `restore_listing.php`

---

### Cél  
A `restore_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó (vagy admin) **visszaállíthassa a logikailag törölt hirdetését**.  
- Csak **PUT/PATCH** kérést enged.  
- Csak a **saját hirdetését** állíthatja vissza a user, vagy admin jogosultsággal bármely hirdetést.  
- Ellenőrzi, hogy a hirdetés létezik, valóban törölt, és jogosult a visszaállításra.  
- Ha a hirdetés nincs törölve, hibát ad.  
- Egységes hibakezelést és válaszformátumot használ.  

---

### Endpoint  
`PUT http://localhost/legora/listings/restore_listing.php`  
`PATCH http://localhost/legora/listings/restore_listing.php`  

---

### Request body (JSON példa – hirdetés visszaállítása)  
```json
{
  "listing_id": 25
}
```  

---

### Response példák  

**200 OK – sikeres visszaállítás**  
```json
{
  "status": "success",
  "message": "Hirdetés sikeresen visszaállítva",
  "data": {
    "listing_id": 25
  }
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges a hirdetés visszaállításához",
  "data": null
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak PUT/PATCH engedélyezett)",
  "data": null
}
```  

---

**422 Unprocessable Entity – érvénytelen vagy hiányzó listing_id**  
```json
{
  "status": "error",
  "message": "Érvénytelen vagy hiányzó listing_id",
  "data": null
}
```  

---

**404 Not Found – hirdetés nem létezik**  
```json
{
  "status": "error",
  "message": "A hirdetés nem található",
  "data": null
}
```  

---

**403 Forbidden – nem a saját hirdetés és nem admin**  
```json
{
  "status": "error",
  "message": "Nincs jogosultságod ennek a hirdetésnek a visszaállítására",
  "data": null
}
```  

---

**409 Conflict – hirdetés nincs törölve**  
```json
{
  "status": "error",
  "message": "A hirdetés nincs törölve, nem szükséges visszaállítani",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data.listing_id` mező tartalmazza a visszaállított hirdetés azonosítóját.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `listing_id` kötelező, nélküle nem történik visszaállítás.  
- Csak a saját hirdetés visszaállítható, kivéve ha a user admin.  
- Ha a hirdetés nincs törölve, a rendszer hibát ad vissza.  

---

## Összegzés  
- Az `restore_listing.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetést visszaállítani, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: login után → create_listing → delete_listing → restore_listing → get_listings → ellenőrzés.  

---







####################################################################################################################
################################################ ORDERS MODUL ######################################################
####################################################################################################################


----------------------------------------------- orders/checkout.php ---------------------------------------------------

### api_endpoints.md – Orders modul / `checkout.php`

---

### Cél  
A `checkout.php` endpoint feladata, hogy a **kosár tartalmából rendelést hozzon létre**.  
- Csak **POST** kérést enged.  
- Csak bejelentkezett user hívhatja meg.  
- Minden eladóhoz külön `orders` rekord készül.  
- A kosár tételei átkerülnek az `order_items` táblába.  
- A `listings.quantity` csökken.  
- A kosár kiürül.  
- Tranzakcióban fut, rollback hiba esetén.  

---

### Endpoint  
`POST http://localhost/legora/orders/checkout.php`  

---

### Request body  
- A kosárból dolgozik, így **nem szükséges body**.  
- A session alapján az aktuális user kosara kerül feldolgozásra.  

---

### Response példák  

**200 OK – sikeres rendelés létrehozás**  
```json
{
  "status": "success",
  "message": "Rendelés(ek) sikeresen létrehozva",
  "data": [
    {
      "order_id": 101,
      "seller_id": 12,
      "total_price": 36000,
      "status": "pending"
    },
    {
      "order_id": 102,
      "seller_id": 15,
      "total_price": 12000,
      "status": "pending"
    }
  ]
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak POST engedélyezett)",
  "data": null
}
```  

---

**400 Bad Request – üres kosár**  
```json
{
  "status": "error",
  "message": "A kosár üres",
  "data": null
}
```  

---

**500 Internal Server Error – nincs elegendő készlet vagy adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Hiba a rendelés létrehozásakor: Nincs elegendő készlet a listing_id=25 termékhez",
  "data": null
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` tömb tartalmazza az elkészült rendelések adatait.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A kosár tartalmát automatikusan feldolgozza, nem kell külön paraméterezni.  
- Több eladó esetén több rendelés jön létre.  
- A rendelés induló státusza mindig `pending`, és bekerül az `order_status_history` táblába.  

---

## Összegzés  
- Az `checkout.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell rendelést létrehozni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: login → add_to_cart → checkout → get_orders → update_status.  

---



----------------------------------------------- orders/get_orders.php ---------------------------------------------------

### api_endpoints.md – Orders modul / `get_orders.php`

---

### Cél  
A `get_orders.php` endpoint feladata, hogy a **bejelentkezett felhasználó rendeléseit listázza**.  
- Csak bejelentkezett user hívhatja meg.  
- Az `orders` táblából minden olyan rekordot lekér, ahol a `buyer_id` = aktuális user.  
- A `users` táblával JOIN-olva megjeleníti az eladó nevét (`seller_name`).  
- A rendeléseket időrendben adja vissza, legfrissebb elöl.  
- Egységes JSON válaszformátumot használ.  

---

### Endpoint  
`GET http://localhost/legora/orders/get_orders.php`  

---

### Request body  
- Nem szükséges body, a session alapján az aktuális user rendelései kerülnek lekérésre.  

---

### Response példák  

**200 OK – sikeres lekérdezés**  
```json
{
  "status": "success",
  "message": "Rendelések listázva",
  "data": [
    {
      "order_id": 101,
      "seller_id": 12,
      "total_price": 36000,
      "status": "pending",
      "ordered_at": "2025-12-02 15:56:00",
      "seller_name": "lego_seller12"
    },
    {
      "order_id": 102,
      "seller_id": 15,
      "total_price": 12000,
      "status": "shipped",
      "ordered_at": "2025-11-30 10:22:00",
      "seller_name": "lego_seller15"
    }
  ]
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```  

---

**200 OK – nincsenek rendelések**  
```json
{
  "status": "success",
  "message": "Rendelések listázva",
  "data": []
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Hiba a rendelés(ek) lekérdezésekor: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` tömb tartalmazza a rendeléseket.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `seller_name` mező segít a felhasználónak az eladó azonosításában.  
- A rendezés miatt a legfrissebb rendelés mindig elöl szerepel.  

---

## 📝 Összegzés  
- Az `get_orders.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell rendeléseket lekérni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: login → add_to_cart → checkout → get_orders → get_order → update_status.  

---





------------------------------------------------------------------------------------------------------------------------
EZ nem ismétlés, ez két külön file!!!:
----------------------------------------------- orders/get_order.php ---------------------------------------------------

### api_endpoints.md – Orders modul / `get_order.php`

---

### Cél  
A `get_order.php` endpoint feladata, hogy a **bejelentkezett felhasználó vagy eladó egy adott rendelés részleteit lekérje**.  
- Csak bejelentkezett user hívhatja meg.  
- Az `order_id` paraméter kötelező (`GET ?id=123`).  
- Ellenőrzi, hogy a rendeléshez tartozik‑e jogosultság (buyer vagy seller).  
- Lekéri az order alapadatait, a rendelés tételeit (`order_items`), valamint a státusztörténetet (`order_status_history`).  
- Egységes JSON válaszformátumot használ.  

---

### Endpoint  
`GET http://localhost/legora/orders/get_order.php?id=<order_id>`  

---

### Request paraméterek  
- `id` *(integer, kötelező)* → a lekérdezni kívánt rendelés azonosítója.  

---

### Response példák  

**200 OK – sikeres lekérdezés**  
```json
{
  "status": "success",
  "message": "Rendelés részletei lekérve",
  "data": {
    "order_id": 101,
    "buyer_id": 5,
    "buyer_name": "andras_buyer",
    "seller_id": 12,
    "seller_name": "lego_seller12",
    "total_price": 36000,
    "status": "pending",
    "ordered_at": "2025-12-02 15:56:00",
    "items": [
      {
        "order_item_id": 1,
        "listing_id": 25,
        "listing_title": "LEGO Star Wars X-Wing",
        "quantity": 2,
        "price_at_order": 18000
      }
    ],
    "status_history": [
      {
        "old_status": null,
        "new_status": "pending",
        "changed_at": "2025-12-02 15:56:00",
        "changed_by": "andras_buyer"
      }
    ]
  }
}
```

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```  

---

**400 Bad Request – hiányzó order_id paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzó order_id paraméter",
  "data": null
}
```  

---

**404 Not Found – nincs jogosultság vagy nem létező rendelés**  
```json
{
  "status": "error",
  "message": "Nincs ilyen rendelés, vagy nincs jogosultságod megtekinteni",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Hiba a rendelés részleteinek lekérdezésekor: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` objektum tartalmazza a rendelés részleteit.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `buyer_name` és `seller_name` segít a felhasználónak azonosítani a rendelésben résztvevőket.  
- Az `items` tömb tartalmazza a rendelés tételeit.  
- A `status_history` tömb mutatja a rendelés státuszváltozásait időrendben.  

---

##  Összegzés  
- Az `get_order.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, paramétereket, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell rendelés részleteit lekérni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: login → add_to_cart → checkout → get_orders → get_order → update_status.  

---




----------------------------------------------- orders/update_status.php ---------------------------------------------------

### api_endpoints.md – Orders modul / `update_status.php`

---

### Cél  
Az `update_status.php` endpoint feladata, hogy a **bejelentkezett felhasználó vagy eladó frissítse egy rendelés státuszát**.  
- Csak bejelentkezett user hívhatja meg.  
- Kötelező paraméterek: `order_id`, `new_status` (JSON body).  
- Jogosultság ellenőrzés: csak bizonyos státuszváltások engedélyezettek, és csak a megfelelő szereplő (buyer vagy seller) hajthatja végre.  
- Az `orders` táblában frissíti a státuszt.  
- Az `order_status_history` táblába naplózza a változást.  
- Tranzakcióban fut, rollback hiba esetén.  

---

### Endpoint  
`PUT http://localhost/legora/orders/update_status.php`  
`PATCH http://localhost/legora/orders/update_status.php`  

---

### Request body (JSON példa)  
```json
{
  "order_id": 101,
  "new_status": "paid"
}
```  

---

### Engedélyezett státuszváltások  

| Régi státusz | Új státusz   | Jogosult szereplő |
|--------------|--------------|-------------------|
| pending      | paid         | buyer             |
| paid         | shipped      | seller            |
| shipped      | completed    | buyer             |
| pending      | cancelled    | buyer vagy seller |

---

### Response példák  

**200 OK – sikeres státuszváltás**  
```json
{
  "status": "success",
  "message": "Státusz sikeresen frissítve",
  "data": {
    "order_id": 101,
    "old_status": "pending",
    "new_status": "paid"
  }
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```  

---

**400 Bad Request – hiányzó paraméterek**  
```json
{
  "status": "error",
  "message": "Hiányzó order_id vagy new_status paraméter",
  "data": null
}
```  

---

**403 Forbidden – érvénytelen státuszváltás vagy jogosultság hiánya**  
```json
{
  "status": "error",
  "message": "Nincs jogosultság a státuszváltáshoz vagy érvénytelen váltás",
  "data": null
}
```  

---

**404 Not Found – nem létező rendelés**  
```json
{
  "status": "error",
  "message": "Nincs ilyen rendelés",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Hiba a státusz frissítésekor: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` objektum tartalmazza a rendelés azonosítóját és a státuszváltás részleteit.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Csak a megengedett státuszváltások működnek, minden más esetben hibát ad.  
- A státuszváltás mindig naplózásra kerül az `order_status_history` táblába.  

---

##  Összegzés  
- Az `update_status.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell státuszt frissíteni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: login → add_to_cart → checkout → get_orders → get_order → update_status.  

---





##################################################################################################################
################################################ CART MODUL ######################################################
##################################################################################################################

----------------------------------------------- cart/add_to_cart.php ---------------------------------------------------

### api_endpoints.md – Cart modul / `add_to_cart.php`

---

### Cél  
Az `add_to_cart.php` endpoint feladata, hogy a **felhasználó kosarához új tételt adjon** vagy frissítse a meglévő mennyiséget.  
- Csak bejelentkezett user hívhatja meg.  
- Kötelező paraméterek: `listing_id`, `quantity` (JSON body).  
- Ellenőrzi, hogy a hirdetés létezik és nincs törölve.  
- Ha a tétel már szerepel a kosárban → mennyiség növelése.  
- Ha nem szerepel → új rekord létrehozása.  
- Egységes JSON válaszformátumot ad vissza.  

---

### Endpoint  
`POST http://localhost/legora/cart/add_to_cart.php`  

---

### Request body (JSON példa)  
```json
{
  "listing_id": 25,
  "quantity": 2
}
```  

---

### Response példák  

**200 OK – új tétel hozzáadva**  
```json
{
  "status": "success",
  "message": "Tétel hozzáadva a kosárhoz",
  "data": {
    "cart_item_id": 101,
    "quantity": 2
  }
}
```  

---

**200 OK – meglévő tétel frissítve**  
```json
{
  "status": "success",
  "message": "Kosár tétel frissítve",
  "data": {
    "cart_item_id": 101,
    "quantity": 5
  }
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak POST engedélyezett)",
  "data": null
}
```  

---

**422 Unprocessable Entity – hiányzó vagy hibás mezők**  
```json
{
  "status": "error",
  "message": "Érvénytelen vagy hiányzó mezők",
  "data": null
}
```  

---

**404 Not Found – nem létező vagy törölt hirdetés**  
```json
{
  "status": "error",
  "message": "A hirdetés nem található vagy törölve lett",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` objektum tartalmazza a kosár tétel azonosítóját és mennyiségét.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Ha a tétel már szerepel a kosárban, a mennyiség automatikusan növekszik.  
- A `cart_item_id` mindig visszatér, így a frontend könnyen tudja azonosítani a kosár elemeit.  

---

##  Összegzés  
- Az `add_to_cart.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell kosárhoz tételt adni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: Listings → Cart (add_to_cart, get_cart, remove_from_cart) → Orders.  

---


----------------------------------------------- cart/get_cart.php ---------------------------------------------------

### api_endpoints.md – Cart modul / `get_cart.php`

---

### Cél  
A `get_cart.php` endpoint feladata, hogy a **bejelentkezett felhasználó kosarát lekérje**.  
- Csak bejelentkezett user hívhatja meg.  
- Válasz: kosár tételek + összegzés (`subtotal`).  
- Minden tételhez csatolja a listings adatait és a LEGO metaadatokat.  
- Egységes JSON válaszformátumot ad vissza.  

---

### Endpoint  
`GET http://localhost/legora/cart/get_cart.php`  

---

### Request  
- Nem szükséges body.  
- A session alapján az aktuális user kosara kerül lekérésre.  

---

### Response példák  

**200 OK – sikeres lekérdezés (van kosár tétel)**  
```json
{
  "status": "success",
  "message": "Kosár lekérve",
  "data": {
    "items": [
      {
        "cart_item_id": 101,
        "cart_quantity": 2,
        "added_at": "2025-12-02 15:56:00",
        "listing_id": 25,
        "item_type": "set",
        "item_id": 75301,
        "price": 18000,
        "item_condition": "new",
        "description": "LEGO Star Wars X-Wing",
        "created_at": "2025-11-20 10:00:00",
        "seller": "lego_seller12",
        "lego_data": {
          "theme": "Star Wars",
          "pieces": 474
        },
        "line_total": "36000.00"
      }
    ],
    "summary": {
      "subtotal": "36000.00"
    }
  }
}
```

---

**200 OK – üres kosár**  
```json
{
  "status": "success",
  "message": "Kosár lekérve",
  "data": {
    "items": [],
    "summary": {
      "subtotal": "0.00"
    }
  }
}
```

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak GET engedélyezett)",
  "data": null
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data.items` tömb tartalmazza a kosár tételeit, a `summary.subtotal` pedig a teljes összeget.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az `items` minden elemhez tartalmazza a hirdetés adatait, az eladó nevét és a LEGO metaadatokat.  
- A `line_total` minden sorhoz kiszámolja a mennyiség × ár értéket.  
- A `subtotal` a kosár teljes összege.  

---

## Összegzés  
- A `get_cart.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell kosarat lekérni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: Listings → Cart (add_to_cart, get_cart, remove_from_cart) → Orders.  

---




----------------------------------------------- cart/remove_from_cart.php --------------------------------------------

### api_endpoints.md – Cart modul / `remove_from_cart.php`

---

### Cél  
A `remove_from_cart.php` endpoint feladata, hogy a **felhasználó kosarából csökkentse egy tétel mennyiségét vagy teljesen eltávolítsa azt**.  
- Csak bejelentkezett user hívhatja meg.  
- Kötelező paraméterek: `listing_id`, `quantity` (JSON body).  
- Ha a meglévő mennyiség nagyobb, mint a kért csökkentés → mennyiség frissítése.  
- Ha a meglévő mennyiség kisebb vagy egyenlő → teljes törlés a kosárból.  
- Egységes JSON válaszformátumot ad vissza.  

---

### Endpoint  
`DELETE http://localhost/legora/cart/remove_from_cart.php`  

---

### Request body (JSON példa)  
```json
{
  "listing_id": 25,
  "quantity": 2
}
```  

---

### Response példák  

**200 OK – mennyiség csökkentve**  
```json
{
  "status": "success",
  "message": "Kosár tétel mennyisége csökkentve",
  "data": {
    "cart_item_id": 101,
    "quantity": 2
  }
}
```  

---

**200 OK – teljes eltávolítás**  
```json
{
  "status": "success",
  "message": "Tétel eltávolítva a kosárból",
  "data": {
    "cart_item_id": 101
  }
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```  

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak DELETE engedélyezett)",
  "data": null
}
```  

---

**422 Unprocessable Entity – hiányzó vagy hibás mezők**  
```json
{
  "status": "error",
  "message": "Érvénytelen vagy hiányzó mezők",
  "data": null
}
```  

---

**404 Not Found – nem létező kosár tétel**  
```json
{
  "status": "error",
  "message": "A tétel nem található a kosárban",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` objektum tartalmazza a kosár tétel azonosítóját és az új mennyiséget (vagy törlés esetén csak az ID‑t).  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `quantity` csak akkor szerepel a válaszban, ha a tétel mennyisége csökkentve lett.  
- Ha a mennyiség kisebb vagy egyenlő, a tétel teljesen törlődik a kosárból.  

---

##  Összegzés  
- A `remove_from_cart.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell kosárból tételt eltávolítani, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: Listings → Cart (add_to_cart, get_cart, remove_from_cart) → Orders.  

---





######################################################################################################################
################################################ RATINGS MODUL #######################################################
######################################################################################################################


----------------------------------------------- ratings/add_rating.php -----------------------------------------------

### api_endpoints.md – Ratings modul / `add_rating.php`

---

### Cél  
Az `add_rating.php` endpoint feladata, hogy a **felhasználó új értékelést adjon egy eladóhoz**, vagy frissítse a meglévőt.  
- Csak bejelentkezett user hívhatja meg.  
- Feltétel: a user vásárolt már az adott eladótól, és van legalább egy `completed` státuszú rendelése.  
- Kötelező paraméterek: `rated_user_id`, `rating` (1–5).  
- Opcionális: `comment`.  
- Ha már létezik értékelés ugyanettől a vásárlótól ugyanarra az eladóra → frissítjük.  
- Egységes JSON válaszformátumot ad vissza.  

---

### Endpoint  
`POST http://localhost/legora/ratings/add_rating.php`  

---

### Request body (JSON példa)  
```json
{
  "rated_user_id": 12,
  "rating": 5,
  "comment": "Gyors szállítás, kiváló eladó!"
}
```  

---

### Response példák  

**200 OK – új értékelés hozzáadva**  
```json
{
  "status": "success",
  "message": "Értékelés sikeresen hozzáadva",
  "data": {
    "rating_id": 201,
    "rating": 5,
    "comment": "Gyors szállítás, kiváló eladó!"
  }
}
```  

---

**200 OK – meglévő értékelés frissítve**  
```json
{
  "status": "success",
  "message": "Értékelés frissítve",
  "data": {
    "rating_id": 201,
    "rating": 4,
    "comment": "Második rendelésnél is korrekt volt."
  }
}
```  

---

**401 Unauthorized – nincs bejelentkezés**  
```json
{
  "status": "error",
  "message": "Bejelentkezés szükséges",
  "data": null
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak POST engedélyezett)",
  "data": null
}
```  

---

**422 Unprocessable Entity – hiányzó vagy hibás mezők**  
```json
{
  "status": "error",
  "message": "Érvénytelen vagy hiányzó mezők (rated_user_id, rating 1-5 között kötelező)",
  "data": null
}
```  

---

**403 Forbidden – önértékelés tiltása**  
```json
{
  "status": "error",
  "message": "Saját magadat nem értékelheted",
  "data": null
}
```  

---

**403 Forbidden – nincs completed rendelés**  
```json
{
  "status": "error",
  "message": "Csak akkor értékelhetsz, ha már vásároltál ettől az eladótól (completed rendelés szükséges)",
  "data": null
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` objektum tartalmazza az értékelés azonosítóját, értékét és kommentet.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Ha már van értékelés, a rendszer automatikusan frissíti azt.  
- Csak akkor lehet értékelni, ha tényleges vásárlás történt az adott eladótól.  

---

##  Összegzés  
- Az `add_rating.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell értékelést hozzáadni vagy frissíteni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: Listings → Cart → Orders → Ratings (add_rating, get_ratings).  

---




----------------------------------------------- ratings/get_ratings.php ----------------------------------------------

### api_endpoints.md – Ratings modul / `get_ratings.php`

---

### Cél  
A `get_ratings.php` endpoint feladata, hogy **egy adott felhasználóhoz tartozó értékeléseket lekérje**.  
- Paraméter: `rated_user_id` (kötelező, GET query paraméter).  
- Visszaadja az összes értékelést, az értékelők felhasználónevével együtt.  
- Kiszámolja az átlagos értékelést is.  
- Egységes JSON válaszformátumot ad vissza.  

---

### Endpoint  
`GET http://localhost/legora/ratings/get_ratings.php?rated_user_id={ID}`  

---

### Request példa  
```
GET http://localhost/legora/ratings/get_ratings.php?rated_user_id=12
```

---

### Response példák  

**200 OK – van értékelés**  
```json
{
  "status": "success",
  "message": "Értékelések lekérve",
  "data": {
    "rated_user_id": 12,
    "average_rating": 4.5,
    "total_ratings": 2,
    "ratings": [
      {
        "rating_id": 301,
        "rating": 5,
        "comment": "Nagyon korrekt eladó!",
        "rated_at": "2025-11-20 10:00:00",
        "rater_username": "buyer123",
        "rater_id": 7
      },
      {
        "rating_id": 302,
        "rating": 4,
        "comment": "Gyors szállítás, de a csomagolás lehetne jobb.",
        "rated_at": "2025-11-18 09:30:00",
        "rater_username": "lego_fan",
        "rater_id": 9
      }
    ]
  }
}
```

---

**200 OK – nincs értékelés**  
```json
{
  "status": "success",
  "message": "Értékelések lekérve",
  "data": {
    "rated_user_id": 99,
    "average_rating": null,
    "total_ratings": 0,
    "ratings": []
  }
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzó rated_user_id paraméter",
  "data": null
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Érvénytelen kérés (csak GET engedélyezett)",
  "data": null
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]",
  "data": null
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` objektum tartalmazza az átlagos értékelést, az értékelések számát és a részletes listát.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az `average_rating` csak akkor számítódik ki, ha van legalább egy értékelés.  
- Az `ratings` tömb minden elemhez tartalmazza az értékelés azonosítóját, értékét, kommentet, időbélyeget és az értékelő felhasználó adatait.  

---

##  Összegzés  
- A `get_ratings.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell értékeléseket lekérni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: Listings → Cart → Orders → Ratings (add_rating, get_ratings).  

---






############################################################################################################################
################################################ ADMIN MODUL ###############################################################
############################################################################################################################





----------------------------------------------- admin/admin_login.php.php --------------------------------------------------

### api_endpoints.md – Admin modul / `admin_login.php`

---

### Cél  
Az `admin_login.php` endpoint feladata, hogy **biztonságos bejelentkezést biztosítson az adminisztrátorok számára**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy a felhasználó létezik, admin szerepkörben van, és aktív.  
- A jelszót `password_verify` segítségével ellenőrzi.  
- Ha minden rendben, létrehoz egy session-t, és visszaadja a sikeres bejelentkezés üzenetét JSON formátumban.  
- Hibás esetekben mindig `status: error` választ küld, egyértelmű üzenettel.  

---

### Endpoint  
`POST http://localhost/legora/admin/admin_login.php`  

---

### Request body (példa)  
```json
{
  "username": "admin_user",
  "password": "correct_password"
}
```  

---

### Response példák  

**200 OK – sikeres bejelentkezés**  
```json
{
  "status": "success",
  "message": "Sikeres admin bejelentkezés.",
  "admin_id": 1,
  "username": "admin_user"
}
```  

---

**401 Unauthorized – hibás jelszó**  
```json
{
  "status": "error",
  "message": "Hibás jelszó."
}
```  

---

**404 Not Found – nem létező felhasználó**  
```json
{
  "status": "error",
  "message": "Nincs ilyen felhasználó."
}
```  

---

**403 Forbidden – nem admin szerepkör**  
```json
{
  "status": "error",
  "message": "Nincs admin jogosultság."
}
```  

---

**403 Forbidden – inaktív felhasználó**  
```json
{
  "status": "error",
  "message": "A felhasználó inaktív."
}
```  

---

**422 Unprocessable Entity – hiányzó paraméterek**  
```json
{
  "status": "error",
  "message": "Hiányzik a felhasználónév vagy jelszó."
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```  

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data` mező helyett közvetlenül az `admin_id` és `username` érkezik.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A session létrejön, így a további admin funkciókhoz már jogosultságot kap a felhasználó.  
- A jelszó ellenőrzése `password_verify`-al történik, tehát a DB-ben `password_hash`-al tárolt jelszó szükséges.  

---

##  Összegzés  
- Az `admin_login.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request body példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell admin bejelentkezést kezelni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul belépési pontja, amely biztosítja a jogosultságot a további admin funkciókhoz.  

---


----------------------------------------------- admin/logout.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `logout.php`

---

### Cél  
A `logout.php` endpoint feladata, hogy **biztonságos kijelentkezést biztosítson az adminisztrátorok számára**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ha van, törli a session változókat, a cookie‑t, és lezárja a session‑t.  
- JSON választ ad vissza: `success` ha sikeres, `error` ha nem volt aktív session.  

---

### Endpoint  
`POST http://localhost/legora/admin/logout.php`  

---

### Request példa  
```
POST http://localhost/legora/admin/logout.php
```

*(Nincs body, csak a session megléte szükséges.)*

---

### Response példák  

**200 OK – sikeres kijelentkezés**  
```json
{
  "status": "success",
  "message": "Sikeres kijelentkezés."
}
```  

---

**401 Unauthorized – nincs aktív session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```  

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```  

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a session törlődött, az admin kilépett.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A kijelentkezés után minden admin funkcióhoz újra be kell jelentkezni (`admin_login.php`).  
- Biztonsági okból csak POST metódus engedélyezett.  

---

##  Összegzés  
- A `logout.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell admin kijelentkezést kezelni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul belépési/kilépési pontja, amely biztosítja a jogosultságok biztonságos kezelését.  

---



----------------------------------------------- admin/admin_get_user_list.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `admin_get_user_list.php`

---

### Cél  
Az `admin_get_user_list.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes felhasználót**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja az összes felhasználót (aktív és inaktív).  
- JSON formátumban adja vissza az adatokat.  

---

### Endpoint  
`GET http://localhost/legora/admin/admin_get_user_list.php`  

---

### Request példa  
```
GET http://localhost/legora/admin/admin_get_user_list.php
```

*(Nincs body, csak a session megléte szükséges.)*

---

### Response példák  

**200 OK – sikeres lekérés (admin_user bejelentkezve)**  
```json
{
  "status": "success",
  "message": "Felhasználók listája lekérve.",
  "data": {
    "users": [
      {
        "id": 1,
        "username": "user1",
        "email": "user1@example.com",
        "role": "user",
        "is_active": 1
      },
      {
        "id": 2,
        "username": "user2",
        "email": "user2@example.com",
        "role": "user",
        "is_active": 1
      },
      {
        "id": 9,
        "username": "user9",
        "email": "user9@example.com",
        "role": "user",
        "is_active": 1
      },
      {
        "id": 13,
        "username": "user13",
        "email": "user13@example.com",
        "role": "user",
        "is_active": 1
      }
      // … további userek
    ]
  }
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Csak GET metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data.users` tömb tartalmazza az összes felhasználót.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A válaszban minden userhez érkezik: `id`, `username`, `email`, `role`, `is_active`.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  

---

##  Összegzés  
- Az `admin_get_user_list.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell felhasználólistát lekérni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul egyik alapfunkciója, a felhasználók áttekintése.  

---



----------------------------------------------- admin/get_user_details.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `get_user_details.php`

---

### Cél  
A `get_user_details.php` endpoint feladata, hogy **az adminisztrátor számára egy adott felhasználó részletes adatait adja vissza**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja a felhasználó alapadatait (ID, username, email, role, is_active, created_at).  
- Visszaadja a felhasználóhoz tartozó hirdetéseket (id, item_type, item_id, price, item_condition, deleted_at).  
- Hibakezelést tartalmaz: hiányzó paraméter, nem létező user, rossz metódus.  

---

### Endpoint  
`GET http://localhost/legora/admin/get_user_details.php?id={user_id}`  

---

### Request példa  
```
GET http://localhost/legora/admin/get_user_details.php?id=9
```

*(Nincs body, csak az `id` paraméter szükséges.)*

---

### Response példák  

**200 OK – sikeres lekérés (admin_user bejelentkezve, `user9`)**  
```json
{
  "status": "success",
  "message": "Felhasználó részletes adatai lekérve.",
  "data": {
    "user": {
      "id": 9,
      "username": "user9",
      "email": "user9@example.com",
      "role": "user",
      "is_active": 1,
      "created_at": "2025-10-13 07:53:37"
    },
    "listings": [
      {
        "id": 1,
        "item_type": "set",
        "item_id": "010423-1",
        "price": 32999,
        "item_condition": "new",
        "deleted_at": null
      },
      {
        "id": 2,
        "item_type": "set",
        "item_id": "001-1",
        "price": 11999,
        "item_condition": "used",
        "deleted_at": null
      },
      {
        "id": 3,
        "item_type": "part",
        "item_id": "73129",
        "price": 199.99,
        "item_condition": "new",
        "deleted_at": null
      },
      {
        "id": 4,
        "item_type": "minifig",
        "item_id": "fig-000008",
        "price": 2999.99,
        "item_condition": "used",
        "deleted_at": null
      },
      {
        "id": 5,
        "item_type": "minifig",
        "item_id": "fig-000009",
        "price": 2799.99,
        "item_condition": "new",
        "deleted_at": null
      }
    ]
  }
}
```

---

**404 Not Found – nem létező user ID**  
```json
{
  "status": "error",
  "message": "Nem található felhasználó ezzel az ID-val."
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzik a felhasználó azonosító (id)."
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Csak GET metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data.user` objektum tartalmazza a felhasználó adatait, a `data.listings` tömb pedig a hirdetéseit.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- A `listings` tömbben minden hirdetéshez érkezik: `id`, `item_type`, `item_id`, `price`, `item_condition`, `deleted_at`.  

---

##  Összegzés  
- A `get_user_details.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell egy felhasználó részletes adatait lekérni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul részletes felhasználókezelési funkciója.  

---



----------------------------------------------- admin/admin_delete_user.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `admin_delete_user.php`

---

### Cél  
Az `admin_delete_user.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy felhasználó soft delete (inaktiválás)** végrehajtását.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a felhasználó.  
- Admin felhasználót nem engedi törölni.  
- Soft delete módon inaktiválja a felhasználót (`is_active = 0`).  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Endpoint  
`POST http://localhost/legora/admin/admin_delete_user.php`  

---

### Request példa  
```json
{
  "id": 9
}
```

*(Body: `id` kötelező paraméter, a törlendő felhasználó azonosítója.)*

---

### Response példák  

**200 OK – sikeres törlés (user9)**  
```json
{
  "status": "success",
  "message": "Felhasználó sikeresen inaktiválva.",
  "user_id": 9,
  "username": "user9"
}
```

---

**404 Not Found – nem létező user ID**  
```json
{
  "status": "error",
  "message": "Nem található felhasználó ezzel az ID-val."
}
```

---

**403 Forbidden – admin törlés tiltva**  
```json
{
  "status": "error",
  "message": "Admin felhasználó nem törölhető."
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzik a felhasználó azonosító (id)."
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `user_id` és `username` mezők jelzik, melyik felhasználó lett inaktiválva.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- Admin felhasználó védett → nem törölhető.  

---

##  Összegzés  
- Az `admin_delete_user.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell felhasználót törölni/inaktiválni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul egyik kulcsfunkciója, a felhasználók biztonságos törlése.  

---



----------------------------------------------- admin/admin_restore_user.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `admin_restore_user.php`

---

### Cél  
Az `admin_restore_user.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy soft delete‑elt (inaktivált) felhasználó visszaállítását**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a felhasználó.  
- Ha a felhasználó már aktív, hibát ad vissza.  
- Soft delete‑elt felhasználót visszaállítja (`is_active = 1`).  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Endpoint  
`POST http://localhost/legora/admin/admin_restore_user.php`  

---

### Request példa  
```json
{
  "id": 9
}
```

*(Body: `id` kötelező paraméter, a visszaállítandó felhasználó azonosítója.)*

---

### Response példák  

**200 OK – sikeres visszaállítás (user9)**  
```json
{
  "status": "success",
  "message": "Felhasználó sikeresen visszaállítva.",
  "user_id": 9,
  "username": "user9"
}
```

---

**404 Not Found – nem létező user ID**  
```json
{
  "status": "error",
  "message": "Nem található felhasználó ezzel az ID-val."
}
```

---

**409 Conflict – már aktív user**  
```json
{
  "status": "error",
  "message": "A felhasználó nincs inaktiválva, így nem állítható vissza."
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzik a felhasználó azonosító (id)."
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `user_id` és `username` mezők jelzik, melyik felhasználó lett visszaállítva.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- Ha a felhasználó már aktív, a rendszer 409 hibát ad vissza, így a frontendnek ezt külön kell kezelnie.  

---

##  Összegzés  
- Az `admin_restore_user.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell felhasználót visszaállítani, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul teljes körű user‑kezelése (törlés + visszaállítás).  

---




----------------------------------------------- admin/toggle_user.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `toggle_user.php`

---

### Cél  
A `toggle_user.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy felhasználó aktív/inaktív státuszának váltását**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a felhasználó.  
- Ha aktív volt, inaktiválja (`is_active = 0`), ha inaktív volt, aktiválja (`is_active = 1`).  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Endpoint  
`POST http://localhost/legora/admin/toggle_user.php`  

---

### Request példa  
```json
{
  "id": 9
}
```

*(Body: `id` kötelező paraméter, a váltandó felhasználó azonosítója.)*

---

### Response példák  

**200 OK – sikeres váltás (user9 aktív → inaktív)**  
```json
{
  "status": "success",
  "message": "A felhasználó inaktiválva lett.",
  "user_id": 9,
  "username": "user9",
  "is_active": 0
}
```

---

**200 OK – sikeres váltás (user13 inaktív → aktív)**  
```json
{
  "status": "success",
  "message": "A felhasználó aktiválva lett.",
  "user_id": 13,
  "username": "user13",
  "is_active": 1
}
```

---

**404 Not Found – nem létező user ID**  
```json
{
  "status": "error",
  "message": "Nem található felhasználó ezzel az ID-val."
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzik a felhasználó azonosító (id)."
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `user_id`, `username` és `is_active` mezők jelzik az új állapotot.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- A váltás mindig bináris: ha aktív volt, inaktiválja; ha inaktív volt, aktiválja.  

---

##  Összegzés  
- Az `toggle_user.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell felhasználó státuszát váltani, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul rugalmas user‑kezelési funkciója (aktiválás/inaktiválás).  

---



----------------------------------------------- admin/admin_get_listings_list.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `admin_get_listings_list.php`

---

### Cél  
Az `admin_get_listings_list.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes hirdetést**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja az összes hirdetést (aktív és soft delete‑elt).  
- JSON formátumban adja vissza az adatokat.  

---

### Endpoint  
`GET http://localhost/legora/admin/admin_get_listings_list.php`  

---

### Request példa  
```
GET http://localhost/legora/admin/admin_get_listings_list.php
```

*(Nincs body, csak a session megléte szükséges.)*

---

### Response példák  

**200 OK – sikeres lekérés (admin_user bejelentkezve)**  
```json
{
  "status": "success",
  "message": "Hirdetések listája lekérve.",
  "data": {
    "listings": [
      {
        "id": 1,
        "title": "LEGO Star Wars X-Wing",
        "description": "Új, bontatlan készlet",
        "price": 32999,
        "user_id": 9,
        "created_at": "2025-10-13 07:53:37",
        "deleted_at": null
      },
      {
        "id": 2,
        "title": "LEGO City Police Station",
        "description": "Használt, hiánytalan",
        "price": 11999,
        "user_id": 9,
        "created_at": "2025-10-14 08:12:11",
        "deleted_at": null
      },
      {
        "id": 5,
        "title": "Batman minifig",
        "description": "Új, bontatlan",
        "price": 2799.99,
        "user_id": 9,
        "created_at": "2025-10-17 11:00:00",
        "deleted_at": "2025-11-01 12:00:00"
      }
    ]
  }
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Csak GET metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `data.listings` tömb tartalmazza az összes hirdetést.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- A `deleted_at` mező jelzi, hogy a hirdetés soft delete‑elt állapotban van.  

---

##  Összegzés  
- Az `admin_get_listings_list.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetéseket listázni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési áttekintő funkciója.  

---



----------------------------------------------- admin/get_deleted_listings.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `get_deleted_listings.php`

---

### Cél  
A `get_deleted_listings.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes törölt (soft delete‑elt) hirdetést**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Lekérdezi a `listings` táblát, ahol `deleted_at IS NOT NULL`.  
- JOIN‑olja a `users` táblát, hogy látszódjon a hirdető neve és emailje.  
- JSON formátumban adja vissza az adatokat.  

---

### Endpoint  
`GET http://localhost/legora/admin/get_deleted_listings.php`  

---

### Request példa  
```
GET http://localhost/legora/admin/get_deleted_listings.php
```

*(Nincs body, csak a session megléte szükséges.)*

---

### Response példák  

**200 OK – sikeres lekérés (admin_user bejelentkezve)**  
```json
{
  "status": "success",
  "message": "Törölt hirdetések listája lekérve.",
  "count": 2,
  "listings": [
    {
      "id": 5,
      "user_id": 9,
      "item_type": "minifig",
      "item_id": "fig-000009",
      "price": 2799.99,
      "item_condition": "new",
      "description": "Batman minifig",
      "deleted_at": "2025-11-01 12:00:00",
      "username": "user9",
      "email": "user9@example.com"
    },
    {
      "id": 7,
      "user_id": 13,
      "item_type": "set",
      "item_id": "1234-1",
      "price": 14999,
      "item_condition": "used",
      "description": "LEGO Castle",
      "deleted_at": "2025-11-05 09:30:00",
      "username": "user13",
      "email": "user13@example.com"
    }
  ]
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Csak GET metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `listings` tömb tartalmazza az összes törölt hirdetést.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- A `deleted_at` mező jelzi, hogy mikor lett törölve a hirdetés.  
- A `username` és `email` mezők segítik az adminot azonosítani a hirdetőt.  

---

##  Összegzés  
- Az `get_deleted_listings.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell törölt hirdetéseket listázni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési funkciója a törölt hirdetések áttekintésére.  

---





----------------------------------------------- admin/admin_restore_listing.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `admin_restore_listing.php`

---

### Cél  
Az `admin_restore_listing.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy soft delete‑elt hirdetés visszaállítását**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a hirdetés.  
- Ha nincs törölve, hibát ad vissza.  
- Soft delete‑elt hirdetést visszaállítja (`deleted_at = NULL`).  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Endpoint  
`POST http://localhost/legora/admin/admin_restore_listing.php`  

---

### Request példa  
```json
{
  "id": 5
}
```

*(Body: `id` kötelező paraméter, a visszaállítandó hirdetés azonosítója.)*

---

### Response példák  

**200 OK – sikeres visszaállítás (Batman minifig)**  
```json
{
  "status": "success",
  "message": "Hirdetés sikeresen visszaállítva.",
  "listing_id": 5,
  "title": "Batman minifig"
}
```

---

**404 Not Found – nem létező hirdetés ID**  
```json
{
  "status": "error",
  "message": "Nem található hirdetés ezzel az ID-val."
}
```

---

**409 Conflict – már aktív hirdetés**  
```json
{
  "status": "error",
  "message": "A hirdetés nincs törölve, így nem állítható vissza."
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzik a hirdetés azonosító (id)."
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `listing_id` és `title` mezők jelzik, melyik hirdetés lett visszaállítva.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- Ha a hirdetés már aktív, a rendszer 409 hibát ad vissza, így a frontendnek ezt külön kell kezelnie.  

---

##  Összegzés  
- Az `admin_restore_listing.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetést visszaállítani, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul teljes körű hirdetéskezelése (törlés + visszaállítás).  

---





----------------------------------------------- admin/admin_delete_listing.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `admin_delete_listing.php`

---

### Cél  
Az `admin_delete_listing.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy hirdetés soft delete‑elését (inaktiválását)**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a hirdetés.  
- Ha már törölve van, hibát ad vissza.  
- Soft delete módon törli a hirdetést (`deleted_at = NOW()`).  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Endpoint  
`POST http://localhost/legora/admin/admin_delete_listing.php`  

---

### Request példa  
```json
{
  "id": 2
}
```

*(Body: `id` kötelező paraméter, a törlendő hirdetés azonosítója.)*

---

### Response példák  

**200 OK – sikeres törlés (LEGO City Police Station)**  
```json
{
  "status": "success",
  "message": "Hirdetés sikeresen törölve (soft delete).",
  "listing_id": 2,
  "title": "LEGO City Police Station"
}
```

---

**404 Not Found – nem létező hirdetés ID**  
```json
{
  "status": "error",
  "message": "Nem található hirdetés ezzel az ID-val."
}
```

---

**409 Conflict – már törölt hirdetés (Batman minifig)**  
```json
{
  "status": "error",
  "message": "A hirdetés már törölve van."
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzik a hirdetés azonosító (id)."
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `listing_id` és `title` mezők jelzik, melyik hirdetés lett törölve.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- Ha a hirdetés már törölve van, a rendszer 409 hibát ad vissza, így a frontendnek ezt külön kell kezelnie.  

---

##  Összegzés  
- Az `admin_delete_listing.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetést törölni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési funkciója a soft delete megvalósítására.  

---




----------------------------------------------- admin/get_users.php ---------------------------------------------------


### api_endpoints.md – `get_users.php`

---

### Cél  
A `get_users.php` endpoint feladata, hogy **listázza az összes felhasználót** (általános user lista, nem admin).  
- Csak GET metódus engedélyezett.  
- Nem igényel admin session.  
- Lekérdezi a `users` táblát, és visszaadja a fő adataikat: `id, username, email, role, is_active, created_at, address, phone`.  
- JSON formátumban adja vissza az adatokat.  

---

### Endpoint  
`GET http://localhost/legora/admin/get_users.php`  

---

### Request példa  
```http
GET /legora/admin/get_users.php HTTP/1.1
Host: localhost
```

*(Nincs szükség body‑ra, mert csak lekérés történik.)*

---

### Response példák  

**200 OK – sikeres lekérés**  
```json
{
  "status": "success",
  "message": "Felhasználók listája lekérve.",
  "count": 3,
  "users": [
    {
      "id": 9,
      "username": "user9",
      "email": "user9@example.com",
      "role": "user",
      "is_active": 1,
      "created_at": "2025-10-13 07:53:37",
      "address": "Budapest, Fő utca 1.",
      "phone": "+36123456789"
    },
    {
      "id": 13,
      "username": "user13",
      "email": "user13@example.com",
      "role": "user",
      "is_active": 0,
      "created_at": "2025-10-14 08:12:11",
      "address": "Debrecen, Kossuth tér 5.",
      "phone": "+36201234567"
    },
    {
      "id": 1,
      "username": "admin",
      "email": "admin@example.com",
      "role": "admin",
      "is_active": 1,
      "created_at": "2025-09-01 09:00:00",
      "address": "Budapest, Admin központ",
      "phone": "+36111111111"
    }
  ]
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Csak GET metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `users` tömb tartalmazza az összes felhasználót.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- A `count` mező segít a frontendnek gyorsan megjeleníteni, hány felhasználó van a listában.  
- A mezők (`id`, `username`, `email`, `role`, `is_active`, `created_at`, `address`, `phone`) közvetlenül használhatók táblázatos megjelenítéshez.  

---

##  Összegzés  
- Az `get_users.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell felhasználókat listázni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az általános user listázás REST alapelvek szerint.  

---





----------------------------------------------- admin/delete_listing.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `delete_listing.php`

---

### Cél  
A `delete_listing.php` az **admin modul** része, amelynek feladata, hogy az adminisztrátor soft delete művelettel törölhessen bármely hirdetést a rendszerből.  
- Csak POST metódus engedélyezett.  
- Admin session szükséges.  
- Ellenőrzi, hogy létezik‑e a hirdetés.  
- Ha már törölve van, hibát ad vissza.  
- Soft delete: `deleted_at = NOW()`.  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Endpoint  
`POST http://localhost/legora/admin/delete_listing.php`  

---

### Request példa  
```json
{
  "id": 7
}
```

*(Body: `id` kötelező paraméter, a törlendő hirdetés azonosítója.)*

---

### Response példák  

**200 OK – sikeres törlés (LEGO Castle)**  
```json
{
  "status": "success",
  "message": "Hirdetés sikeresen törölve (soft delete).",
  "listing_id": 7,
  "title": "LEGO Castle"
}
```

---

**404 Not Found – nem létező hirdetés ID**  
```json
{
  "status": "error",
  "message": "Nem található hirdetés ezzel az ID-val."
}
```

---

**409 Conflict – már törölt hirdetés (Batman minifig)**  
```json
{
  "status": "error",
  "message": "A hirdetés már törölve van."
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzik a hirdetés azonosító (id)."
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `listing_id` és `title` mezők jelzik, melyik hirdetés lett törölve.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- Ha a hirdetés már törölve van, a rendszer 409 hibát ad vissza, így a frontendnek ezt külön kell kezelnie.  

---

##  Összegzés  
- Az `delete_listing.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetést törölni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési funkciója a soft delete megvalósítására.  

---




----------------------------------------------- admin/restore_listing.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `restore_listing.php`

---

### Cél  
A `restore_listing.php` az **admin modul** része, amelynek feladata, hogy az adminisztrátor soft delete művelettel törölt hirdetéseket visszaállíthasson.  
- Csak POST metódus engedélyezett.  
- Admin session szükséges.  
- Ellenőrzi, hogy létezik‑e a hirdetés és valóban törölt állapotban van‑e.  
- Visszaállítás: `deleted_at = NULL`.  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Endpoint  
`POST http://localhost/legora/admin/restore_listing.php`  

---

### Request példa  
```json
{
  "id": 5
}
```

*(Body: `id` kötelező paraméter, a visszaállítandó hirdetés azonosítója.)*

---

### Response példák  

**200 OK – sikeres visszaállítás (Batman minifig)**  
```json
{
  "status": "success",
  "message": "Hirdetés sikeresen visszaállítva.",
  "listing_id": 5,
  "title": "Batman minifig"
}
```

---

**404 Not Found – nem létező hirdetés ID**  
```json
{
  "status": "error",
  "message": "Nem található hirdetés ezzel az ID-val."
}
```

---

**409 Conflict – nem törölt hirdetés (LEGO Castle)**  
```json
{
  "status": "error",
  "message": "A hirdetés nincs törölt állapotban, így nem állítható vissza."
}
```

---

**422 Unprocessable Entity – hiányzó paraméter**  
```json
{
  "status": "error",
  "message": "Hiányzik a hirdetés azonosító (id)."
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – GET kérés**  
```json
{
  "status": "error",
  "message": "Csak POST metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `listing_id` és `title` mezők jelzik, melyik hirdetés lett visszaállítva.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- Ha a hirdetés nincs törölt állapotban, a rendszer 409 hibát ad vissza, így a frontendnek ezt külön kell kezelnie.  

---

##  Összegzés  
- Az `restore_listing.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell hirdetést visszaállítani, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési funkciója a soft delete visszaállítására.  

---





----------------------------------------------- admin/get_stats.php ---------------------------------------------------

### api_endpoints.md – Admin modul / `get_stats.php`

---

### Cél  
A `get_stats.php` az **admin modul** része, amely összesítő statisztikákat ad vissza a rendszer állapotáról.  
- Csak GET metódus engedélyezett.  
- Admin session szükséges.  
- Visszaadja az aktív/törölt hirdetések és felhasználók számát.  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Endpoint  
`GET http://localhost/legora/admin/get_stats.php`  

---

### Request példa  
```http
GET /legora/admin/get_stats.php HTTP/1.1
Host: localhost
```

*(Nincs szükség body‑ra, mert csak lekérés történik.)*

---

### Response példák  

**200 OK – sikeres lekérés**  
```json
{
  "status": "success",
  "message": "Statisztikák sikeresen lekérve.",
  "active_listings": 12,
  "deleted_listings": 3,
  "active_users": 8,
  "inactive_users": 2,
  "total_users": 10
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Csak GET metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a statisztikai értékek integerként jelennek meg (`active_listings`, `deleted_listings`, `active_users`, `inactive_users`, `total_users`).  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- A metódus mindig GET → ha más metódust használ a kliens, 405 hibát kap.  

---

##  Összegzés  
- Az `get_stats.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell statisztikákat lekérni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul statisztikai funkciója a dashboardhoz.  

---






----------------------------------------------- admin/get_all_stats.php.php ---------------------------------------------------


### api_endpoints.md – Admin modul / `get_all_stats.php`

---

### Cél  
A `get_all_stats.php` az **admin modul** része, amely komplex statisztikákat ad vissza a rendszer állapotáról.  
- Csak GET metódus engedélyezett.  
- Admin session szükséges.  
- Visszaadja:  
  - Globális számok (aktív/törölt hirdetések, aktív/inaktív/összes user).  
  - Felhasználónkénti bontás: hány hirdetésük van, abból mennyi aktív/törölt.  
  - Hirdetésenkénti összesítés: átlagár, minimum, maximum.  
- JSON formátumban adja vissza az adatokat.  

---

### Endpoint  
`GET http://localhost/legora/admin/get_all_stats.php`  

---

### Request példa  
```http
GET /legora/admin/get_all_stats.php HTTP/1.1
Host: localhost
```

*(Nincs szükség body‑ra, mert csak lekérés történik.)*

---

### Response példák  

**200 OK – sikeres lekérés**  
```json
{
  "status": "success",
  "message": "Komplex statisztikák sikeresen lekérve.",
  "global_stats": {
    "active_listings": 12,
    "deleted_listings": 3,
    "active_users": 8,
    "inactive_users": 2,
    "total_users": 10
  },
  "user_stats": [
    {
      "id": 1,
      "username": "admin",
      "email": "admin@example.com",
      "is_active": 1,
      "total_listings": 5,
      "active_listings": 4,
      "deleted_listings": 1
    },
    {
      "id": 9,
      "username": "user9",
      "email": "user9@example.com",
      "is_active": 1,
      "total_listings": 3,
      "active_listings": 2,
      "deleted_listings": 1
    }
  ],
  "listing_stats": {
    "total_listings": 15,
    "avg_price": 120.5,
    "min_price": 50,
    "max_price": 300
  }
}
```

---

**401 Unauthorized – nincs aktív admin session**  
```json
{
  "status": "error",
  "message": "Nincs aktív admin session."
}
```

---

**405 Method Not Allowed – POST kérés**  
```json
{
  "status": "error",
  "message": "Csak GET metódus engedélyezett."
}
```

---

**500 Internal Server Error – adatbázis hiba**  
```json
{
  "status": "error",
  "message": "Adatbázis hiba: [hibaüzenet]"
}
```

---

### Megjegyzés a frontend számára  
- A `status` mezőt figyeljék:  
  - `success` → a `global_stats`, `user_stats`, `listing_stats` objektumok tartalmazzák a részletes adatokat.  
  - `error` → a `message` mezőt jelenítsék meg a felhasználónak.  
- Az admin session megléte kötelező → ha nincs, a végpont 401 hibát ad vissza.  
- A metódus mindig GET → ha más metódust használ a kliens, 405 hibát kap.  

---

##  Összegzés  
- Az `get_all_stats.php` dokumentációja most **egységes sablonban** van leírva.  
- Tartalmazza az **URL‑t, metódust, request példát, response példákat és megjegyzéseket**.  
- A frontend fejlesztők így pontosan tudják, hogyan kell komplex statisztikákat lekérni, és mit várhatnak vissza.  
- Vizsgán jól bemutatható: az admin modul statisztikai funkciója globális, felhasználói és hirdetés szintű összesítést ad.  

---


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