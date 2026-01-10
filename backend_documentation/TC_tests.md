<!-- ##################################################################################################################
################################################ AUTH MODUL ######################################################
##################################################################################################################

----------------------------------------------- auth/login.php ---------------------------------------------------


## Thunder Client tesztforgatókönyv – Auth modul / login.php

### Végpont: `auth/login.php`

#### Lehetséges tesztek (áttekintés)
- **Sikeres bejelentkezés** (POST) – helyes email/username + jelszó, aktív fiók  
- **Hibás jelszó** (POST) – helyes email/username, rossz jelszó  
- **Inaktív fiók** (POST) – helyes adatok, de `is_active = 0`  
- **Nem létező felhasználó** (POST) – email/username nincs az adatbázisban  
- **Hiányzó mezők** (POST) – pl. nincs jelszó vagy email  
- **Érvénytelen JSON** (POST) – rossz formátumú body  
- **Érvénytelen metódus** (GET) – csak POST engedélyezett  

---

#### Részletes tesztpéldák

**1. Sikeres bejelentkezés**
- Request:
  - Method: POST  
  - URL: `http://localhost/legora/auth/login.php`  
  - Headers: `Content-Type: application/json`  
  - Body:
    ```json
    {
      "email_or_username": "user9@example.com",
      "password": "1234"
    }
    ```
- Response (200 OK):
    ```json
    {
      "status": "success",
      "message": "Sikeres bejelentkezés",
      "data": {
        "user_id": 9,
        "username": "user9",
        "email": "user9@example.com"
      }
    }
    ```

---

**2. Hibás jelszó**
- Body:
    ```json
    {
      "email_or_username": "user9@example.com",
      "password": "rosszjelszo"
    }
    ```
- Response (401 Unauthorized):
    ```json
    {
      "status": "error",
      "message": "Hibás jelszó",
      "data": null
    }
    ```

---

**3. Inaktív fiók**
- Body:
    ```json
    {
      "email_or_username": "inactiveuser@example.com",
      "password": "1234"
    }
    ```
- Response (403 Forbidden):
    ```json
    {
      "status": "error",
      "message": "A fiók nincs aktiválva. Kérlek, ellenőrizd az e‑mail fiókodat.",
      "data": null
    }
    ```

---

**4. Nem létező felhasználó**
- Body:
    ```json
    {
      "email_or_username": "nincsilyen@example.com",
      "password": "1234"
    }
    ```
- Response (401 Unauthorized):
    ```json
    {
      "status": "error",
      "message": "Hibás felhasználónév vagy e‑mail",
      "data": null
    }
    ```

---

**5. Hiányzó mezők**
- Body:
    ```json
    {
      "email_or_username": "user9@example.com"
    }
    ```
- Response (422 Unprocessable Entity):
    ```json
    {
      "status": "error",
      "message": "Minden mező kitöltése kötelező (email_or_username, password)",
      "data": null
    }
    ```

---

**6. Érvénytelen JSON**
- Body: `{"email_or_username": "user9@example.com", "password": }`  
- Response (400 Bad Request):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen JSON formátum",
      "data": null
    }
    ```

---

**7. Érvénytelen metódus**
- Request: GET `http://localhost/legora/auth/login.php`  
- Response (405 Method Not Allowed):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak POST engedélyezett)",
      "data": null
    }
    ```

---

#### Összefoglaló táblázat

| Teszt                  | Input                        | Várt válasz | HTTP kód | Eredmény |
|-------------------------|------------------------------|-------------|----------|----------|
| Sikeres bejelentkezés   | helyes email + jelszó        | success     | 200      | ok       |
| Hibás jelszó            | rossz jelszó                 | error       | 401      | ok       |
| Inaktív fiók            | is_active=0                  | error       | 403      | ok       |
| Nem létező felhasználó  | email nincs DB‑ben           | error       | 401      | ok       |
| Hiányzó mezők           | password hiányzik            | error       | 422      | ok       |
| Érvénytelen JSON        | hibás body                   | error       | 400      | ok       |
| Érvénytelen metódus     | GET request                  | error       | 405      | ok       |

---

## Összegzés
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **robosztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.

---



----------------------------------------------- auth/logout.php ---------------------------------------------------


## Thunder Client tesztforgatókönyv – Auth modul / logout.php


```

### Végpont: `auth/logout.php`

#### Lehetséges tesztek (áttekintés)
- **Sikeres kijelentkezés** (POST) – aktív session esetén  
- **Nincs aktív bejelentkezés** (POST) – ha nincs `user_id` a session‑ben  
- **Érvénytelen metódus** (GET) – csak POST engedélyezett  

---

#### Részletes tesztpéldák

**1. Sikeres kijelentkezés**
- Request:
  - Method: POST  
  - URL: `http://localhost/legora/auth/logout.php`  
  - Headers: `Content-Type: application/json`  
  - Body: *nincs szükség body‑ra*  
- Response (200 OK):
    ```json
    {
      "status": "success",
      "message": "Sikeres kijelentkezés",
      "data": null
    }
    ```

---

**2. Nincs aktív bejelentkezés**
- Request: POST ugyanazzal az URL‑lel, de session nélkül  
- Response (401 Unauthorized):
    ```json
    {
      "status": "error",
      "message": "Nincs aktív bejelentkezés",
      "data": null
    }
    ```

---

**3. Érvénytelen metódus**
- Request: GET `http://localhost/legora/auth/logout.php`  
- Response (405 Method Not Allowed):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak POST engedélyezett)",
      "data": null
    }
    ```

---

#### Összefoglaló táblázat

| Teszt                   | Input             | Várt válasz | HTTP kód | Eredmény |
|--------------------------|------------------|-------------|----------|----------|
| Sikeres kijelentkezés    | aktív session    | success     | 200      | ok       |
| Nincs aktív bejelentkezés| session hiányzik | error       | 401      | ok       |
| Érvénytelen metódus      | GET request      | error       | 405      | ok       |

---

##  Összegzés
- A logout endpoint tesztjei lefedik a tipikus és hibás eseteket.  
- A frontend fejlesztők pontosan látják, hogy mikor kapnak `success` és mikor `error` választ.  
- Jól bemutatható: először bejelentkezés, majd logout → session törlődik, és a válasz jelzi a sikeres kijelentkezést.




----------------------------------------------- auth/register.php ---------------------------------------------------


##  Thunder Client tesztforgatókönyv – Auth modul / register.php


### Végpont: `auth/register.php`

#### Lehetséges tesztek (áttekintés)
- **Sikeres regisztráció** (POST) – új user létrehozása helyes adatokkal  
- **Duplikált regisztráció** (POST) – már létező email/username  
- **Hibás email formátum** (POST) – nem valid email  
- **Tiltólistás email** (POST) – pl. `teszt@teszt.com`  
- **Hibás CAPTCHA** (POST) – rossz kód  
- **Hiányzó mezők** (POST) – pl. nincs jelszó vagy email  
- **Érvénytelen JSON** (POST) – rossz formátumú body  
- **Érvénytelen metódus** (GET) – csak POST engedélyezett  

---

#### Részletes tesztpéldák

**1. Sikeres regisztráció**
- Request:
  - Method: POST  
  - URL: `http://localhost/legora/auth/register.php`  
  - Headers: `Content-Type: application/json`  
  - Body:
    ```json
    {
      "username": "tesztuser",
      "email": "tesztuser@example.com",
      "password": "Test123!",
      "captcha": "1234"
    }
    ```
- Response (201 Created):
    ```json
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
    ```

---

**2. Duplikált regisztráció**
- Body:
    ```json
    {
      "username": "tesztuser",
      "email": "tesztuser@example.com",
      "password": "Test123!",
      "captcha": "1234"
    }
    ```
- Response (409 Conflict):
    ```json
    {
      "status": "error",
      "message": "Ez az e‑mail vagy felhasználónév már foglalt",
      "data": null
    }
    ```

---

**3. Hibás email formátum**
- Body:
    ```json
    {
      "username": "rossz",
      "email": "nememail",
      "password": "Test123!",
      "captcha": "1234"
    }
    ```
- Response (422 Unprocessable Entity):
    ```json
    {
      "status": "error",
      "message": "Hibás email formátum",
      "data": null
    }
    ```

---

**4. Tiltólistás email**
- Body:
    ```json
    {
      "username": "rossz",
      "email": "teszt@teszt.com",
      "password": "Test123!",
      "captcha": "1234"
    }
    ```
- Response (422 Unprocessable Entity):
    ```json
    {
      "status": "error",
      "message": "Ez az e‑mail cím nem engedélyezett",
      "data": null
    }
    ```

---

**5. Hibás CAPTCHA**
- Body:
    ```json
    {
      "username": "rossz",
      "email": "rossz@example.com",
      "password": "Test123!",
      "captcha": "9999"
    }
    ```
- Response (403 Forbidden):
    ```json
    {
      "status": "error",
      "message": "Hibás CAPTCHA",
      "data": null
    }
    ```

---

**6. Hiányzó mezők**
- Body:
    ```json
    {
      "username": "tesztuser",
      "email": "tesztuser@example.com"
    }
    ```
- Response (422 Unprocessable Entity):
    ```json
    {
      "status": "error",
      "message": "Minden mező kitöltése kötelező (username, email, password, captcha)",
      "data": null
    }
    ```

---

**7. Érvénytelen JSON**
- Body: `{"username": "tesztuser", "email": "tesztuser@example.com", "password": }`  
- Response (400 Bad Request):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen JSON formátum",
      "data": null
    }
    ```

---

**8. Érvénytelen metódus**
- Request: GET `http://localhost/legora/auth/register.php`  
- Response (405 Method Not Allowed):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak POST engedélyezett)",
      "data": null
    }
    ```

---

#### Összefoglaló táblázat

| Teszt                 | Input                        | Várt válasz | HTTP kód | Eredmény |
|------------------------|------------------------------|-------------|----------|----------|
| Sikeres regisztráció   | helyes adatok                | success     | 201      | ok       |
| Duplikált regisztráció | meglévő email/username       | error       | 409      | ok       |
| Hibás email            | email=nememail              | error       | 422      | ok       |
| Tiltólistás email      | email=teszt@teszt.com       | error       | 422      | ok       |
| Hibás CAPTCHA          | captcha=9999                | error       | 403      | ok       |
| Hiányzó mezők          | password hiányzik           | error       | 422      | ok       |
| Érvénytelen JSON       | hibás body                  | error       | 400      | ok       |
| Érvénytelen metódus    | GET request                 | error       | 405      | ok       |

---

## Összegzés
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **robosztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.

---



----------------------------------------------- auth/verify.php ---------------------------------------------------

## Thunder Client tesztforgatókönyv – Auth modul / verify.php

### Végpont: `auth/verify.php`

#### Lehetséges tesztek (áttekintés)
- **Sikeres aktiválás** (GET) – érvényes token, inaktív fiók → aktiválás sikeres  
- **Hiányzó token** (GET) – nincs `token` paraméter → hiba  
- **Érvénytelen token** (GET) – nem létező token → hiba  
- **Már aktivált token** (GET) – token létezik, de fiók már aktív → hiba  
- **Adatbázis hiba** (GET) – DB kapcsolat vagy lekérdezés hiba  

---

#### Részletes tesztpéldák

**1. Sikeres aktiválás**
- Request:
  - Method: GET  
  - URL: `http://localhost/legora/auth/verify.php?token=VALIDTOKEN123`  
- Response (200 OK):
    ```json
    {
      "status": "success",
      "message": "Fiók sikeresen aktiválva",
      "data": null
    }
    ```

---

**2. Hiányzó token**
- Request: `http://localhost/legora/auth/verify.php`  
- Response (400 Bad Request):
    ```json
    {
      "status": "error",
      "message": "Hiányzó token",
      "data": null
    }
    ```

---

**3. Érvénytelen token**
- Request: `http://localhost/legora/auth/verify.php?token=FAKETOKEN999`  
- Response (400 Bad Request):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen vagy már aktivált token",
      "data": null
    }
    ```

---

**4. Már aktivált token**
- Request: `http://localhost/legora/auth/verify.php?token=ALREADYUSEDTOKEN`  
- Response (400 Bad Request):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen vagy már aktivált token",
      "data": null
    }
    ```

---

**5. Adatbázis hiba**
- Szimulált DB hiba esetén  
- Response (500 Internal Server Error):
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

#### Összefoglaló táblázat

| Teszt                | Input                  | Várt válasz | HTTP kód | Eredmény |
|-----------------------|------------------------|-------------|----------|----------|
| Sikeres aktiválás     | token=VALIDTOKEN123    | success     | 200      | ok       |
| Hiányzó token         | nincs token paraméter  | error       | 400      | ok       |
| Érvénytelen token     | token=FAKETOKEN999     | error       | 400      | ok       |
| Már aktivált token    | token=ALREADYUSEDTOKEN | error       | 400      | ok       |
| Adatbázis hiba        | DB error               | error       | 500      | ok       |

---

## Összegzés
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **robosztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.




###################################################################################################################
################################################ USERS MODUL ######################################################
###################################################################################################################

-----------------------------------------------users/get_user.php ------------------------------------------------


## Thunder Client tesztforgatókönyv – Users modul / get_user.php


### Végpont: `users/get_user.php`

#### Lehetséges tesztek (áttekintés)
- **Sikeres lekérés** (GET) – bejelentkezett user, létező adatok  
- **Nincs bejelentkezés** (GET) – session hiányzik  
- **Felhasználó nem található** (GET) – session van, de user törölve vagy nem létezik  
- **Érvénytelen metódus** (POST) – csak GET engedélyezett  
- **Adatbázis hiba** – DB kapcsolat vagy lekérdezés hiba  

---

#### Részletes tesztpéldák

**1. Sikeres lekérés**
- Request:
  - Method: GET  
  - URL: `http://localhost/legora/users/get_user.php`  
  - Headers: `Content-Type: application/json`  
  - Session: aktív (pl. login után)  
- Response (200 OK):
    ```json
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
    ```

---

**2. Nincs bejelentkezés**
- Request: GET ugyanazzal az URL‑lel, de session nélkül  
- Response (401 Unauthorized):
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**3. Felhasználó nem található**
- Request: GET, session van, de az adott user törölve  
- Response (404 Not Found):
    ```json
    {
      "status": "error",
      "message": "Felhasználó nem található",
      "data": null
    }
    ```

---

**4. Érvénytelen metódus**
- Request: POST `http://localhost/legora/users/get_user.php`  
- Response (405 Method Not Allowed):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak GET engedélyezett)",
      "data": null
    }
    ```

---

**5. Adatbázis hiba**
- Szimulált DB hiba esetén  
- Response (500 Internal Server Error):
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

#### Összefoglaló táblázat

| Teszt                  | Input             | Várt válasz | HTTP kód | Eredmény |
|-------------------------|------------------|-------------|----------|----------|
| Sikeres lekérés         | aktív session    | success     | 200      | ok       |
| Nincs bejelentkezés     | session hiányzik | error       | 401      | ok       |
| Felhasználó nem található| törölt user      | error       | 404      | ok       |
| Érvénytelen metódus     | POST request     | error       | 405      | ok       |
| Adatbázis hiba          | DB error         | error       | 500      | ok       |

---

## Összegzés
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztők pontosan látják, hogy mikor kapnak `success` és mikor `error` választ.  
- Jól bemutatható: login → get_user → user adatok megjelenítése.






---------------------------------------------- users/update_user.php ------------------------------------------------


## Thunder Client tesztforgatókönyv – Users modul / update_user.php

### Végpont: `users/update_user.php`

#### Lehetséges tesztek (áttekintés)
- **Sikeres frissítés** – email, username, password, address, phone  
- **Nincs bejelentkezés** – session hiányzik  
- **Nincs frissíthető mező** – üres body vagy nem támogatott mezők  
- **Érvénytelen metódus** – pl. GET vagy POST  
- **Hibás formátum** – email, password, address, phone validáció bukik  
- **Adatbázis hiba** – DB kapcsolat vagy lekérdezés hiba  

---

#### Részletes tesztpéldák

**1. Sikeres frissítés – email + phone**
- Request:
  - Method: PUT  
  - URL: `http://localhost/legora/users/update_user.php`  
  - Headers: `Content-Type: application/json`  
  - Body:
    ```json
    {
      "email": "newmail@example.com",
      "phone": "+36 30 123 4567"
    }
    ```
- Response (200 OK):
    ```json
    {
      "status": "success",
      "message": "Felhasználói adatok frissítve",
      "data": {
        "updated_fields": ["email", "phone"]
      }
    }
    ```

---

**2. Sikeres frissítés – address + username**
- Body:
    ```json
    {
      "username": "newusername",
      "address": "8200 Veszprém, Kossuth utca 10."
    }
    ```
- Response (200 OK):
    ```json
    {
      "status": "success",
      "message": "Felhasználói adatok frissítve",
      "data": {
        "updated_fields": ["username", "address"]
      }
    }
    ```

---

**3. Nincs bejelentkezés**
- Request: PUT ugyanazzal az URL‑lel, de session nélkül  
- Response (401 Unauthorized):
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**4. Nincs frissíthető mező**
- Body:
    ```json
    {
      "age": 25
    }
    ```
- Response (422 Unprocessable Entity):
    ```json
    {
      "status": "error",
      "message": "Nincs frissíthető mező megadva",
      "data": null
    }
    ```

---

**5. Hibás formátum – phone**
- Body:
    ```json
    {
      "phone": "abc123"
    }
    ```
- Response (422 Unprocessable Entity):
    ```json
    {
      "status": "error",
      "message": "Hibás telefonszám formátum",
      "data": null
    }
    ```

---

**6. Hibás formátum – address**
- Body:
    ```json
    {
      "address": "utca"
    }
    ```
- Response (422 Unprocessable Entity):
    ```json
    {
      "status": "error",
      "message": "Hibás lakcím formátum",
      "data": null
    }
    ```

---

**7. Érvénytelen metódus**
- Request: GET `http://localhost/legora/users/update_user.php`  
- Response (405 Method Not Allowed):
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak PUT/PATCH engedélyezett)",
      "data": null
    }
    ```

---

**8. Adatbázis hiba**
- Szimulált DB hiba esetén  
- Response (500 Internal Server Error):
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

#### Összefoglaló táblázat

| Teszt                  | Input                        | Várt válasz | HTTP kód | Eredmény |
|-------------------------|------------------------------|-------------|----------|----------|
| Sikeres frissítés       | email/username/password/address/phone | success | 200 | ok |
| Nincs bejelentkezés     | session hiányzik             | error       | 401      | ok |
| Nincs frissíthető mező  | nem támogatott mezők         | error       | 422      | ok |
| Hibás formátum – phone  | phone = "abc123"             | error       | 422      | ok |
| Hibás formátum – address| address = "utca"             | error       | 422      | ok |
| Érvénytelen metódus     | GET request                  | error       | 405      | ok |
| Adatbázis hiba          | DB error                     | error       | 500      | ok |

---

## Összegzés
- A tesztforgatókönyv lefedi az új `address` és `phone` mezők validációját.  
- A frontend fejlesztők pontosan látják, mikor kapnak `success` és mikor `error` választ.  
- Jól bemutatható: login → update_user → get_user → ellenőrzés, hogy az új mezők tényleg frissültek.  

---










######################################################################################################################
################################################ LISTINGS MODUL ######################################################
######################################################################################################################


### Végpont: `listings/get_listings.php`

### Thunder Client tesztforgatókönyv – Listings modul / `get_listings.php`

---

### Cél  
A `get_listings.php` endpoint feladata, hogy a piactér hirdetéseit listázza.  
- Csak **GET** kérést enged.  
- Lapozás (`page`, `limit`) és szűrés (`item_type`, `seller_id`) támogatott.  
- Csak aktív hirdetések (`deleted_at IS NULL`).  
- LEGO metaadatok (`lego_data`) a helperen keresztül kerülnek be.  
- Egységes JSON válasz formátumot ad vissza.

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérés** – alapértelmezett paraméterekkel.  
- **Lapozás teszt** – `page` és `limit` paraméterekkel.  
- **Szűrés item_type szerint** – pl. csak `set` típusú hirdetések.  
- **Szűrés seller_id szerint** – adott eladó hirdetései.  
- **Érvénytelen metódus** – pl. POST kérés.  
- **Adatbázis hiba szimuláció** – hibás SQL vagy kapcsolat.  

---

### Részletes tesztpéldák

**1. Sikeres lekérés (alapértelmezett)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/listings/get_listings.php`  
- Response (200 OK):  
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

**2. Lapozás teszt**  
- Request:  
  - GET `http://localhost/legora/listings/get_listings.php?page=2&limit=5`  
- Response:  
  - 200 OK, 5 elem visszaadva, `page=2`.

---

**3. Szűrés item_type szerint**  
- Request:  
  - GET `http://localhost/legora/listings/get_listings.php?item_type=set`  
- Response:  
  - Csak `set` típusú hirdetések jelennek meg.

---

**4. Szűrés seller_id szerint**  
- Request:  
  - GET `http://localhost/legora/listings/get_listings.php?seller_id=9`  
- Response:  
  - Csak a `user9` által feltöltött hirdetések jelennek meg.

---

**5. Érvénytelen metódus**  
- Request:  
  - POST `http://localhost/legora/listings/get_listings.php`  
- Response (405 Method Not Allowed):  
  ```json
  {
    "status": "error",
    "message": "Érvénytelen kérés (csak GET engedélyezett)",
    "data": null
  }
  ```

---

**6. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
  ```json
  {
    "status": "error",
    "message": "Adatbázis hiba: [hibaüzenet]",
    "data": null
  }
  ```

---

### Összefoglaló táblázat

| Teszt                   | Input                          | Várt válasz | HTTP kód | Eredmény |
|--------------------------|--------------------------------|-------------|----------|----------|
| Sikeres lekérés          | alapértelmezett paraméterek    | success     | 200      | ok       |
| Lapozás                  | page=2, limit=5                | success     | 200      | ok       |
| Szűrés item_type szerint | item_type=set                  | success     | 200      | ok       |
| Szűrés seller_id szerint | seller_id=9                    | success     | 200      | ok       |
| Érvénytelen metódus      | POST request                   | error       | 405      | ok       |
| Adatbázis hiba           | hibás SQL / kapcsolat          | error       | 500      | ok       |

---

## Összegzés
- A Thunder Client tesztek lefedik a tipikus és hibás eseteket.  
- A frontend fejlesztőknek látszik, hogy a végpont **robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---



----------------------------------------------- listings/create_listing.php -------------------------------------------------

### 🎯 Thunder Client tesztforgatókönyv – Listings modul / `create_listing.php`

---

### Cél  
A `create_listing.php` endpoint feladata, hogy új hirdetést hozzon létre a piactéren.  
- Csak **POST** kérést enged.  
- Csak **bejelentkezett felhasználó** adhat fel hirdetést.  
- Validálja a bemenetet (`item_type`, `item_id`, `quantity`, `price`, `item_condition`).  
- Ellenőrzi, hogy a megadott LEGO elem valóban létezik a statikus adatbázisban.  
- Mentés után visszaadja a hirdetés adatait JSON formátumban.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres hirdetés létrehozás** – helyes adatokkal.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen JSON** – hibás body.  
- **Érvénytelen item_type** – pl. `theme`.  
- **Érvénytelen item_condition** – pl. `broken`.  
- **Hiányzó vagy hibás adatok** – pl. `quantity=0`, `price=0`.  
- **Nem létező LEGO elem** – pl. `item_id=XYZ123`.  
- **Érvénytelen metódus** – GET kérés.  
- **Adatbázis hiba szimuláció** – hibás SQL.  

---

### Részletes tesztpéldák

**1. Sikeres hirdetés létrehozás**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/listings/create_listing.php`  
  - Headers: `Content-Type: application/json`  
  - Body:  
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
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges a hirdetés feladásához",
      "data": null
    }
    ```

---

**3. Érvénytelen JSON**  
- Body: `{"item_type": "set", "item_id": }`  
- Response (400 Bad Request):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen JSON formátum",
      "data": null
    }
    ```

---

**4. Érvénytelen item_type**  
- Body:  
    ```json
    {
      "item_type": "theme",
      "item_id": "010423-1",
      "quantity": 1,
      "price": 1000,
      "item_condition": "new"
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen item_type (set, part, minifig megengedett)",
      "data": null
    }
    ```

---

**5. Érvénytelen item_condition**  
- Body:  
    ```json
    {
      "item_type": "set",
      "item_id": "010423-1",
      "quantity": 1,
      "price": 1000,
      "item_condition": "broken"
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen item_condition (new, used megengedett)",
      "data": null
    }
    ```

---

**6. Hiányzó vagy hibás adatok**  
- Body:  
    ```json
    {
      "item_type": "set",
      "item_id": "",
      "quantity": 0,
      "price": 0,
      "item_condition": "new"
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzó vagy hibás adatok (item_id, quantity, price kötelező)",
      "data": null
    }
    ```

---

**7. Nem létező LEGO elem**  
- Body:  
    ```json
    {
      "item_type": "set",
      "item_id": "XYZ123",
      "quantity": 1,
      "price": 1000,
      "item_condition": "new"
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "A megadott LEGO elem nem található az adatbázisban",
      "data": null
    }
    ```

---

**8. Érvénytelen metódus**  
- Request: GET `http://localhost/legora/listings/create_listing.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak POST engedélyezett)",
      "data": null
    }
    ```

---

**9. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                     | Input                          | Várt válasz | HTTP kód | Eredmény |
|----------------------------|--------------------------------|-------------|----------|----------|
| Sikeres hirdetés           | helyes adatok                  | success     | 200      | ok       |
| Nincs bejelentkezés        | session hiányzik               | error       | 401      | ok       |
| Érvénytelen JSON           | hibás body                     | error       | 400      | ok       |
| Érvénytelen item_type      | item_type=theme                | error       | 422      | ok       |
| Érvénytelen item_condition | item_condition=broken          | error       | 422      | ok       |
| Hiányzó/hibás adatok       | item_id üres, quantity=0       | error       | 422      | ok       |
| Nem létező LEGO elem       | item_id=XYZ123                 | error       | 422      | ok       |
| Érvénytelen metódus        | GET request                    | error       | 405      | ok       |
| Adatbázis hiba             | hibás SQL / kapcsolat          | error       | 500      | ok       |

---

## Összegzés
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---








----------------------------------------------- listings/update_listing.php ---------------------------------------------------



###  Thunder Client tesztforgatókönyv – Listings modul / `update_listing.php`

---

### Cél  
A `update_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó **módosíthassa a saját hirdetését**.  
- Csak **PUT/PATCH** kérést enged.  
- Csak a **saját hirdetését** frissítheti a user.  
- Csak bizonyos mezők frissíthetők: `quantity`, `price`, `item_condition`, `description`.  
- Ellenőrzi, hogy a hirdetés létezik, nem törölt, és valóban a bejelentkezett userhez tartozik.  
- Dinamikusan építi az SQL-t, így csak a megadott mezők frissülnek.  
- Egységes hibakezelést és válaszformátumot használ.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres frissítés** – helyes adatokkal.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen metódus** – pl. GET.  
- **Érvénytelen vagy hiányzó listing_id**.  
- **Nincs frissíthető mező megadva**.  
- **Hirdetés nem létezik**.  
- **Nem a saját hirdetés**.  
- **Törölt hirdetés módosítása**.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres frissítés**  
- Request:  
  - Method: PUT  
  - URL: `http://localhost/legora/listings/update_listing.php`  
  - Headers: `Content-Type: application/json`  
  - Body:  
    ```json
    {
      "listing_id": 25,
      "price": 18000,
      "description": "Frissített leírás"
    }
    ```  
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges a hirdetés módosításához",
      "data": null
    }
    ```

---

**3. Érvénytelen metódus**  
- Request: GET `http://localhost/legora/listings/update_listing.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak PUT/PATCH engedélyezett)",
      "data": null
    }
    ```

---

**4. Érvénytelen vagy hiányzó listing_id**  
- Body:  
    ```json
    {
      "listing_id": 0,
      "price": 1000
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen vagy hiányzó listing_id",
      "data": null
    }
    ```

---

**5. Nincs frissíthető mező megadva**  
- Body:  
    ```json
    {
      "listing_id": 25
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Nincs frissíthető mező megadva",
      "data": null
    }
    ```

---

**6. Hirdetés nem létezik**  
- Body:  
    ```json
    {
      "listing_id": 9999,
      "price": 1000
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés nem található",
      "data": null
    }
    ```

---

**7. Nem a saját hirdetés**  
- Body:  
    ```json
    {
      "listing_id": 30,
      "price": 2000
    }
    ```  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "Nincs jogosultságod ennek a hirdetésnek a módosítására",
      "data": null
    }
    ```

---

**8. Törölt hirdetés módosítása**  
- Body:  
    ```json
    {
      "listing_id": 40,
      "price": 2000
    }
    ```  
- Response (409 Conflict):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés már törölve lett, nem módosítható",
      "data": null
    }
    ```

---

**9. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                     | Input                          | Várt válasz | HTTP kód | Eredmény |
|----------------------------|--------------------------------|-------------|----------|----------|
| Sikeres frissítés          | helyes adatok                  | success     | 200      | ok       |
| Nincs bejelentkezés        | session hiányzik               | error       | 401      | ok       |
| Érvénytelen metódus        | GET request                    | error       | 405      | ok       |
| Érvénytelen listing_id     | listing_id=0                   | error       | 422      | ok       |
| Nincs frissíthető mező     | csak listing_id                | error       | 422      | ok       |
| Hirdetés nem létezik       | listing_id=9999                | error       | 404      | ok       |
| Nem a saját hirdetés       | más user hirdetése             | error       | 403      | ok       |
| Törölt hirdetés            | deleted_at != null             | error       | 409      | ok       |
| Adatbázis hiba             | hibás SQL / kapcsolat          | error       | 500      | ok       |

---

## Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---





----------------------------------------------- listings/delete_listing.php ---------------------------------------------------

### Thunder Client tesztforgatókönyv – Listings modul / `delete_listing.php`

---

### Cél  
A `delete_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó **logikailag törölje a saját hirdetését**.  
- Csak **DELETE** kérést enged.  
- Csak a **saját hirdetését** törölheti a user.  
- Nem fizikai törlés történik, hanem a `deleted_at` mező kitöltése → így később visszaállítható (`restore_listing.php`).  
- Ellenőrzi, hogy a hirdetés létezik, nem törölt, és valóban a bejelentkezett userhez tartozik.  
- Egységes hibakezelést és válaszformátumot használ.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres törlés** – helyes adatokkal.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen metódus** – pl. GET.  
- **Érvénytelen vagy hiányzó listing_id**.  
- **Hirdetés nem létezik**.  
- **Nem a saját hirdetés**.  
- **Már törölt hirdetés**.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres törlés**  
- Request:  
  - Method: DELETE  
  - URL: `http://localhost/legora/listings/delete_listing.php`  
  - Headers: `Content-Type: application/json`  
  - Body:  
    ```json
    {
      "listing_id": 25
    }
    ```  
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges a hirdetés törléséhez",
      "data": null
    }
    ```

---

**3. Érvénytelen metódus**  
- Request: GET `http://localhost/legora/listings/delete_listing.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak DELETE engedélyezett)",
      "data": null
    }
    ```

---

**4. Érvénytelen vagy hiányzó listing_id**  
- Body:  
    ```json
    {
      "listing_id": 0
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen vagy hiányzó listing_id",
      "data": null
    }
    ```

---

**5. Hirdetés nem létezik**  
- Body:  
    ```json
    {
      "listing_id": 9999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés nem található",
      "data": null
    }
    ```

---

**6. Nem a saját hirdetés**  
- Body:  
    ```json
    {
      "listing_id": 30
    }
    ```  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "Nincs jogosultságod ennek a hirdetésnek a törlésére",
      "data": null
    }
    ```

---

**7. Már törölt hirdetés**  
- Body:  
    ```json
    {
      "listing_id": 40
    }
    ```  
- Response (409 Conflict):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés már törölve lett korábban",
      "data": null
    }
    ```

---

**8. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                 | Input              | Várt válasz | HTTP kód | Eredmény |
|------------------------|--------------------|-------------|----------|----------|
| Sikeres törlés         | helyes adatok      | success     | 200      | ok       |
| Nincs bejelentkezés    | session hiányzik   | error       | 401      | ok       |
| Érvénytelen metódus    | GET request        | error       | 405      | ok       |
| Érvénytelen listing_id | listing_id=0       | error       | 422      | ok       |
| Hirdetés nem létezik   | listing_id=9999    | error       | 404      | ok       |
| Nem a saját hirdetés   | más user hirdetése | error       | 403      | ok       |
| Már törölt hirdetés    | deleted_at != null | error       | 409      | ok       |
| Adatbázis hiba         | hibás SQL          | error       | 500      | ok       |

---

## Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---




----------------------------------------------- listings/restore_listing.php ---------------------------------------------------


### Thunder Client tesztforgatókönyv – Listings modul / `restore_listing.php`

---

### Cél  
A `restore_listing.php` endpoint feladata, hogy egy bejelentkezett felhasználó (vagy admin) **visszaállíthassa a logikailag törölt hirdetését**.  
- Csak **PUT/PATCH** kérést enged.  
- Csak a **saját hirdetését** állíthatja vissza a user, vagy admin jogosultsággal bármely hirdetést.  
- Ellenőrzi, hogy a hirdetés létezik, valóban törölt, és jogosult a visszaállításra.  
- Ha a hirdetés nincs törölve, hibát ad.  
- Egységes hibakezelést és válaszformátumot használ.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres visszaállítás** – helyes adatokkal.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen metódus** – pl. GET.  
- **Érvénytelen vagy hiányzó listing_id**.  
- **Hirdetés nem létezik**.  
- **Nem a saját hirdetés és nem admin**.  
- **Hirdetés nincs törölve**.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres visszaállítás**  
- Request:  
  - Method: PUT  
  - URL: `http://localhost/legora/listings/restore_listing.php`  
  - Headers: `Content-Type: application/json`  
  - Body:  
    ```json
    {
      "listing_id": 25
    }
    ```  
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges a hirdetés visszaállításához",
      "data": null
    }
    ```

---

**3. Érvénytelen metódus**  
- Request: GET `http://localhost/legora/listings/restore_listing.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak PUT/PATCH engedélyezett)",
      "data": null
    }
    ```

---

**4. Érvénytelen vagy hiányzó listing_id**  
- Body:  
    ```json
    {
      "listing_id": 0
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen vagy hiányzó listing_id",
      "data": null
    }
    ```

---

**5. Hirdetés nem létezik**  
- Body:  
    ```json
    {
      "listing_id": 9999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés nem található",
      "data": null
    }
    ```

---

**6. Nem a saját hirdetés és nem admin**  
- Body:  
    ```json
    {
      "listing_id": 30
    }
    ```  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "Nincs jogosultságod ennek a hirdetésnek a visszaállítására",
      "data": null
    }
    ```

---

**7. Hirdetés nincs törölve**  
- Body:  
    ```json
    {
      "listing_id": 40
    }
    ```  
- Response (409 Conflict):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés nincs törölve, nem szükséges visszaállítani",
      "data": null
    }
    ```

---

**8. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                        | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------------|--------------------|-------------|----------|----------|
| Sikeres visszaállítás         | helyes adatok      | success     | 200      | ok       |
| Nincs bejelentkezés           | session hiányzik   | error       | 401      | ok       |
| Érvénytelen metódus           | GET request        | error       | 405      | ok       |
| Érvénytelen listing_id        | listing_id=0       | error       | 422      | ok       |
| Hirdetés nem létezik          | listing_id=9999    | error       | 404      | ok       |
| Nem saját hirdetés, nem admin | más user hirdetése | error       | 403      | ok       |
| Hirdetés nincs törölve        | deleted_at IS NULL | error       | 409      | ok       |
| Adatbázis hiba                | hibás SQL          | error       | 500      | ok       |

---

## Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---






####################################################################################################################
################################################ ORDERS MODUL ######################################################
####################################################################################################################

----------------------------------------------- orders/checkout.php ---------------------------------------------------


### Thunder Client tesztforgatókönyv – Orders modul / `checkout.php`

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

### Lehetséges tesztek (áttekintés)
- **Sikeres rendelés létrehozás** – kosárban több termék, több eladó.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen metódus** – pl. GET.  
- **Üres kosár** – nincs tétel.  
- **Nincs elegendő készlet** – kosárban több darab, mint a készlet.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák

**1. Sikeres rendelés létrehozás**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/orders/checkout.php`  
  - Headers: `Content-Type: application/json`  
  - Body: üres (kosárból dolgozik)  
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**3. Érvénytelen metódus**  
- Request: GET `http://localhost/legora/orders/checkout.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak POST engedélyezett)",
      "data": null
    }
    ```

---

**4. Üres kosár**  
- Response (400 Bad Request):  
    ```json
    {
      "status": "error",
      "message": "A kosár üres",
      "data": null
    }
    ```

---

**5. Nincs elegendő készlet**  
- Példa: kosárban 5 db, de készletben csak 3 db.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Hiba a rendelés létrehozásakor: Nincs elegendő készlet a listing_id=25 termékhez",
      "data": null
    }
    ```

---

**6. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Hiba a rendelés létrehozásakor: Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                   | Input             | Várt válasz | HTTP kód | Eredmény |
|--------------------------|------------------|-------------|----------|----------|
| Sikeres rendelés         | kosárban termékek | success     | 200      | ok       |
| Nincs bejelentkezés      | session hiányzik | error       | 401      | ok       |
| Érvénytelen metódus      | GET request      | error       | 405      | ok       |
| Üres kosár               | nincs tétel      | error       | 400      | ok       |
| Nincs elegendő készlet   | több darab mint stock | error | 500      | ok       |
| Adatbázis hiba           | hibás SQL        | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---





----------------------------------------------- orders/get_orders.php ---------------------------------------------------


### Thunder Client tesztforgatókönyv – Orders modul / `get_orders.php`

---

### Cél  
A `get_orders.php` endpoint feladata, hogy a **bejelentkezett felhasználó rendeléseit listázza**.  
- Csak bejelentkezett user hívhatja meg.  
- Az `orders` táblából minden olyan rekordot lekér, ahol a `buyer_id` = aktuális user.  
- A `users` táblával JOIN-olva megjeleníti az eladó nevét (`seller_name`).  
- A rendeléseket időrendben adja vissza, legfrissebb elöl.  
- Egységes JSON válaszformátumot használ.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérdezés** – van rendelés.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Nincsenek rendelések** – üres lista.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák

**1. Sikeres lekérdezés**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/orders/get_orders.php`  
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**3. Nincsenek rendelések**  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Rendelések listázva",
      "data": []
    }
    ```

---

**4. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Hiba a rendelés(ek) lekérdezésekor: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt               | Input             | Várt válasz | HTTP kód | Eredmény |
|----------------------|------------------|-------------|----------|----------|
| Sikeres lekérdezés   | van rendelés     | success     | 200      | ok       |
| Nincs bejelentkezés  | session hiányzik | error       | 401      | ok       |
| Nincsenek rendelések | üres lista       | success     | 200      | ok       |
| Adatbázis hiba       | hibás SQL        | error       | 500      | ok       |

---

## Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---



------------------------------------------------------------------------------------------------------------------------
EZ nem ismétlés, ez két külön file!!!:
----------------------------------------------- orders/get_order.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Orders modul / `get_order.php`

---

### Cél  
A `get_order.php` endpoint feladata, hogy a **bejelentkezett felhasználó vagy eladó egy adott rendelés részleteit lekérje**.  
- Csak bejelentkezett user hívhatja meg.  
- Az `order_id` paraméter kötelező (`GET ?id=123`).  
- Ellenőrzi, hogy a rendeléshez tartozik‑e jogosultság (buyer vagy seller).  
- Lekéri az order alapadatait, a rendelés tételeit (`order_items`), valamint a státusztörténetet (`order_status_history`).  
- Egységes JSON válaszformátumot használ.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérdezés** – jogosult user, létező rendelés.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Hiányzó order_id paraméter** – nincs `?id=`.  
- **Nincs jogosultság vagy nem létező rendelés**.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres lekérdezés**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/orders/get_order.php?id=101`  
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**3. Hiányzó order_id paraméter**  
- Request: `http://localhost/legora/orders/get_order.php`  
- Response (400 Bad Request):  
    ```json
    {
      "status": "error",
      "message": "Hiányzó order_id paraméter",
      "data": null
    }
    ```

---

**4. Nincs jogosultság vagy nem létező rendelés**  
- Request: `http://localhost/legora/orders/get_order.php?id=9999`  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nincs ilyen rendelés, vagy nincs jogosultságod megtekinteni",
      "data": null
    }
    ```

---

**5. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Hiba a rendelés részleteinek lekérdezésekor: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                          | Input                  | Várt válasz | HTTP kód | Eredmény |
|--------------------------------|------------------------|-------------|----------|----------|
| Sikeres lekérdezés             | id=101, jogosult user  | success     | 200      | ok       |
| Nincs bejelentkezés            | session hiányzik       | error       | 401      | ok       |
| Hiányzó order_id paraméter      | nincs ?id=             | error       | 400      | ok       |
| Nincs jogosultság / nem létezik | id=9999                | error       | 404      | ok       |
| Adatbázis hiba                 | hibás SQL              | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---




----------------------------------------------- orders/update_status.php ---------------------------------------------------


###  Thunder Client tesztforgatókönyv – Orders modul / `update_status.php`

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

### Lehetséges tesztek (áttekintés)
- **Sikeres státuszváltás** – jogosult user, érvényes váltás.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Hiányzó paraméterek** – nincs `order_id` vagy `new_status`.  
- **Érvénytelen státuszváltás vagy jogosultság hiánya**.  
- **Nem létező rendelés**.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres státuszváltás (buyer fizet)**  
- Request:  
  - Method: PUT  
  - URL: `http://localhost/legora/orders/update_status.php`  
  - Body:  
    ```json
    {
      "order_id": 101,
      "new_status": "paid"
    }
    ```  
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**3. Hiányzó paraméterek**  
- Body:  
    ```json
    {
      "order_id": 101
    }
    ```  
- Response (400 Bad Request):  
    ```json
    {
      "status": "error",
      "message": "Hiányzó order_id vagy new_status paraméter",
      "data": null
    }
    ```

---

**4. Érvénytelen státuszváltás vagy jogosultság hiánya**  
- Példa: buyer próbálja `shipped` státuszra állítani.  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "Nincs jogosultság a státuszváltáshoz vagy érvénytelen váltás",
      "data": null
    }
    ```

---

**5. Nem létező rendelés**  
- Body:  
    ```json
    {
      "order_id": 9999,
      "new_status": "paid"
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nincs ilyen rendelés",
      "data": null
    }
    ```

---

**6. Adatbázis hiba szimuláció**  
- Példa: hibás SQL vagy kapcsolat megszakad.  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Hiba a státusz frissítésekor: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                          | Input                         | Várt válasz | HTTP kód | Eredmény |
|--------------------------------|-------------------------------|-------------|----------|----------|
| Sikeres státuszváltás          | buyer → pending→paid          | success     | 200      | ok       |
| Nincs bejelentkezés            | session hiányzik              | error       | 401      | ok       |
| Hiányzó paraméterek            | nincs new_status              | error       | 400      | ok       |
| Érvénytelen váltás/jogosultság | buyer próbál shipped-re váltani | error     | 403      | ok       |
| Nem létező rendelés            | order_id=9999                 | error       | 404      | ok       |
| Adatbázis hiba                 | hibás SQL                     | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---





##################################################################################################################
################################################ CART MODUL ######################################################
##################################################################################################################

----------------------------------------------- cart/add_to_cart.php ---------------------------------------------------


###  Thunder Client tesztforgatókönyv – Cart modul / `add_to_cart.php`

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

### Lehetséges tesztek (áttekintés)
- **Sikeres hozzáadás** – új tétel kerül a kosárba.  
- **Sikeres frissítés** – meglévő tétel mennyisége növekszik.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen metódus** – pl. GET.  
- **Hiányzó vagy hibás mezők** – pl. nincs `listing_id` vagy `quantity < 1`.  
- **Nem létező vagy törölt hirdetés**.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres hozzáadás (új tétel)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/cart/add_to_cart.php`  
  - Body:  
    ```json
    {
      "listing_id": 25,
      "quantity": 2
    }
    ```  
- Response (200 OK):  
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

**2. Sikeres frissítés (már létező tétel)**  
- Body:  
    ```json
    {
      "listing_id": 25,
      "quantity": 3
    }
    ```  
- Response (200 OK):  
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

**3. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**4. Érvénytelen metódus (GET)**  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak POST engedélyezett)",
      "data": null
    }
    ```

---

**5. Hiányzó vagy hibás mezők**  
- Body:  
    ```json
    {
      "listing_id": 25,
      "quantity": 0
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen vagy hiányzó mezők",
      "data": null
    }
    ```

---

**6. Nem létező vagy törölt hirdetés**  
- Body:  
    ```json
    {
      "listing_id": 9999,
      "quantity": 1
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés nem található vagy törölve lett",
      "data": null
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                     | Input                        | Várt válasz | HTTP kód | Eredmény |
|----------------------------|------------------------------|-------------|----------|----------|
| Sikeres hozzáadás          | új tétel                    | success     | 200      | ok       |
| Sikeres frissítés          | meglévő tétel               | success     | 200      | ok       |
| Nincs bejelentkezés        | session hiányzik            | error       | 401      | ok       |
| Érvénytelen metódus        | GET request                 | error       | 405      | ok       |
| Hiányzó/hibás mezők        | quantity=0                  | error       | 422      | ok       |
| Nem létező/törölt hirdetés | listing_id=9999             | error       | 404      | ok       |
| Adatbázis hiba             | hibás SQL                   | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **robosztus és biztonságos**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---


----------------------------------------------- cart/get_cart.php ---------------------------------------------------

### Thunder Client tesztforgatókönyv – Cart modul / `get_cart.php`

---

### Cél  
A `get_cart.php` endpoint feladata, hogy a **bejelentkezett felhasználó kosarát lekérje**.  
- Csak bejelentkezett user hívhatja meg.  
- Válasz: kosár tételek + összegzés (`subtotal`).  
- Minden tételhez csatolja a listings adatait és a LEGO metaadatokat.  
- Egységes JSON válaszformátumot ad vissza.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérdezés** – van kosár tétel.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen metódus** – pl. POST.  
- **Üres kosár** – nincs tétel.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres lekérdezés**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/cart/get_cart.php`  
- Response (200 OK):  
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

**2. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**3. Érvénytelen metódus (POST)**  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak GET engedélyezett)",
      "data": null
    }
    ```

---

**4. Üres kosár**  
- Response (200 OK):  
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

**5. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt               | Input             | Várt válasz | HTTP kód | Eredmény |
|----------------------|------------------|-------------|----------|----------|
| Sikeres lekérdezés   | van kosár tétel  | success     | 200      | ok       |
| Nincs bejelentkezés  | session hiányzik | error       | 401      | ok       |
| Érvénytelen metódus  | POST request     | error       | 405      | ok       |
| Üres kosár           | nincs tétel      | success     | 200      | ok       |
| Adatbázis hiba       | hibás SQL        | error       | 500      | ok       |

---

## Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **robosztus és biztonságos**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---




----------------------------------------------- cart/remove_from_cart.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Cart modul / `remove_from_cart.php`

---

### Cél  
A `remove_from_cart.php` endpoint feladata, hogy a **felhasználó kosarából csökkentse egy tétel mennyiségét vagy teljesen eltávolítsa azt**.  
- Csak bejelentkezett user hívhatja meg.  
- Kötelező paraméterek: `listing_id`, `quantity` (JSON body).  
- Ha a meglévő mennyiség nagyobb, mint a kért csökkentés → mennyiség frissítése.  
- Ha a meglévő mennyiség kisebb vagy egyenlő → teljes törlés a kosárból.  
- Egységes JSON válaszformátumot ad vissza.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres mennyiségcsökkentés** – meglévő mennyiség nagyobb, mint a kért csökkentés.  
- **Teljes eltávolítás** – meglévő mennyiség kisebb vagy egyenlő.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen metódus** – pl. POST.  
- **Hiányzó vagy hibás mezők** – pl. nincs `listing_id` vagy `quantity < 1`.  
- **Nem létező kosár tétel**.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres mennyiségcsökkentés**  
- Request:  
  - Method: DELETE  
  - URL: `http://localhost/legora/cart/remove_from_cart.php`  
  - Body:  
    ```json
    {
      "listing_id": 25,
      "quantity": 1
    }
    ```  
- Response (200 OK):  
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

**2. Teljes eltávolítás**  
- Body:  
    ```json
    {
      "listing_id": 25,
      "quantity": 3
    }
    ```  
- Response (200 OK):  
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

**3. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**4. Érvénytelen metódus (POST)**  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak DELETE engedélyezett)",
      "data": null
    }
    ```

---

**5. Hiányzó vagy hibás mezők**  
- Body:  
    ```json
    {
      "listing_id": 25,
      "quantity": 0
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen vagy hiányzó mezők",
      "data": null
    }
    ```

---

**6. Nem létező kosár tétel**  
- Body:  
    ```json
    {
      "listing_id": 9999,
      "quantity": 1
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "A tétel nem található a kosárban",
      "data": null
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input                  | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-----------------------|-------------|----------|----------|
| Sikeres mennyiségcsökkentés | quantity kisebb mint meglévő | success     | 200      | ok       |
| Teljes eltávolítás     | quantity >= meglévő   | success     | 200      | ok       |
| Nincs bejelentkezés    | session hiányzik      | error       | 401      | ok       |
| Érvénytelen metódus    | POST request          | error       | 405      | ok       |
| Hiányzó/hibás mezők    | quantity=0            | error       | 422      | ok       |
| Nem létező tétel       | listing_id=9999       | error       | 404      | ok       |
| Adatbázis hiba         | hibás SQL             | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **robosztus és biztonságos**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---




######################################################################################################################
################################################ RATINGS MODUL #######################################################
######################################################################################################################


----------------------------------------------- ratings/add_rating.php -----------------------------------------------

### Thunder Client tesztforgatókönyv – Ratings modul / `add_rating.php`

---

### Cél  
Az `add_rating.php` endpoint feladata, hogy a **felhasználó új értékelést adjon egy eladóhoz**, vagy frissítse a meglévőt.  
- Csak bejelentkezett user hívhatja meg.  
- Feltétel: a user vásárolt már az adott eladótól, és van legalább egy `completed` státuszú rendelése.  
- Kötelező paraméterek: `rated_user_id`, `rating` (1–5).  
- Opcionális: `comment`.  
- Ha már létezik értékelés ugyanettől a vásárlótól ugyanarra az eladóra → frissítjük.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres új értékelés** – minden feltétel teljesül, nincs korábbi értékelés.  
- **Sikeres frissítés** – már létezik értékelés, új értékekkel frissítjük.  
- **Nincs bejelentkezés** – session hiányzik.  
- **Érvénytelen metódus** – pl. GET.  
- **Hiányzó vagy hibás mezők** – pl. nincs `rated_user_id` vagy `rating` kívül esik az 1–5 tartományon.  
- **Önértékelés tiltása** – user saját magát próbálja értékelni.  
- **Nincs completed rendelés** – nem jogosult értékelésre.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres új értékelés**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/ratings/add_rating.php`  
  - Body:  
    ```json
    {
      "rated_user_id": 12,
      "rating": 5,
      "comment": "Gyors szállítás, kiváló eladó!"
    }
    ```  
- Response (200 OK):  
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

**2. Sikeres frissítés (létező értékelés)**  
- Body:  
    ```json
    {
      "rated_user_id": 12,
      "rating": 4,
      "comment": "Második rendelésnél is korrekt volt."
    }
    ```  
- Response (200 OK):  
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

**3. Nincs bejelentkezés**  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Bejelentkezés szükséges",
      "data": null
    }
    ```

---

**4. Érvénytelen metódus (GET)**  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak POST engedélyezett)",
      "data": null
    }
    ```

---

**5. Hiányzó vagy hibás mezők**  
- Body:  
    ```json
    {
      "rated_user_id": 12,
      "rating": 6
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen vagy hiányzó mezők (rated_user_id, rating 1-5 között kötelező)",
      "data": null
    }
    ```

---

**6. Önértékelés tiltása**  
- Body:  
    ```json
    {
      "rated_user_id": 5,
      "rating": 3
    }
    ```  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "Saját magadat nem értékelheted",
      "data": null
    }
    ```

---

**7. Nincs completed rendelés**  
- Body:  
    ```json
    {
      "rated_user_id": 15,
      "rating": 4
    }
    ```  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "Csak akkor értékelhetsz, ha már vásároltál ettől az eladótól (completed rendelés szükséges)",
      "data": null
    }
    ```

---

**8. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input                         | Várt válasz | HTTP kód | Eredmény |
|-------------------------|------------------------------|-------------|----------|----------|
| Sikeres új értékelés    | rated_user_id=12, rating=5   | success     | 200      | ok       |
| Sikeres frissítés       | meglévő értékelés            | success     | 200      | ok       |
| Nincs bejelentkezés     | session hiányzik             | error       | 401      | ok       |
| Érvénytelen metódus     | GET request                  | error       | 405      | ok       |
| Hiányzó/hibás mezők     | rating=6                     | error       | 422      | ok       |
| Önértékelés tiltása     | rated_user_id = rater_id     | error       | 403      | ok       |
| Nincs completed rendelés| nincs jogosultság            | error       | 403      | ok       |
| Adatbázis hiba          | hibás SQL                    | error       | 500      | ok       |

---

## Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---



----------------------------------------------- ratings/get_ratings.php ----------------------------------------------

###  Thunder Client tesztforgatókönyv – Ratings modul / `get_ratings.php`

---

### Cél  
A `get_ratings.php` endpoint feladata, hogy **egy adott felhasználóhoz tartozó értékeléseket lekérje**.  
- Paraméter: `rated_user_id` (kötelező, GET query paraméter).  
- Visszaadja az összes értékelést, az értékelők felhasználónevével együtt.  
- Kiszámolja az átlagos értékelést is.  
- Egységes JSON válaszformátumot ad vissza.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérdezés** – van értékelés az adott userhez.  
- **Üres értékeléslista** – nincs értékelés.  
- **Hiányzó paraméter** – nincs `rated_user_id`.  
- **Érvénytelen metódus** – pl. POST.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres lekérdezés (van értékelés)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/ratings/get_ratings.php?rated_user_id=12`  
- Response (200 OK):  
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

**2. Üres értékeléslista**  
- Request:  
  - URL: `http://localhost/legora/ratings/get_ratings.php?rated_user_id=99`  
- Response (200 OK):  
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

**3. Hiányzó paraméter**  
- Request:  
  - URL: `http://localhost/legora/ratings/get_ratings.php`  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzó rated_user_id paraméter",
      "data": null
    }
    ```

---

**4. Érvénytelen metódus (POST)**  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Érvénytelen kérés (csak GET engedélyezett)",
      "data": null
    }
    ```

---

**5. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]",
      "data": null
    }
    ```

---

### Összefoglaló táblázat

| Teszt                | Input                         | Várt válasz | HTTP kód | Eredmény |
|-----------------------|------------------------------|-------------|----------|----------|
| Sikeres lekérdezés    | rated_user_id=12             | success     | 200      | ok       |
| Üres értékeléslista   | rated_user_id=99             | success     | 200      | ok       |
| Hiányzó paraméter     | nincs rated_user_id          | error       | 422      | ok       |
| Érvénytelen metódus   | POST request                 | error       | 405      | ok       |
| Adatbázis hiba        | hibás SQL                    | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **robosztus és kiszámítható**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---




############################################################################################################################
################################################ ADMIN MODUL ###############################################################
############################################################################################################################





----------------------------------------------- admin/admin_login.php.php --------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `admin_login.php`

---

### Cél  
Az `admin_login.php` endpoint feladata, hogy **biztonságos bejelentkezést biztosítson az adminisztrátorok számára**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy a felhasználó létezik, admin szerepkörben van, és aktív.  
- A jelszót `password_verify` segítségével ellenőrzi.  
- Siker esetén session létrehozása és JSON válasz.  
- Hibás esetekben mindig `status: error` választ küld, egyértelmű üzenettel.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres bejelentkezés** – helyes admin felhasználónév és jelszó.  
- **Hibás jelszó** – létező admin, de rossz jelszó.  
- **Nem létező felhasználó** – nincs ilyen username.  
- **Nem admin szerepkör** – user létezik, de nem admin.  
- **Inaktív felhasználó** – admin, de `is_active = 0`.  
- **Hiányzó paraméterek** – nincs username vagy password.  
- **Érvénytelen metódus** – pl. GET.  
- **Adatbázis hiba szimuláció**.  

---

### Részletes tesztpéldák

**1. Sikeres bejelentkezés**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/admin_login.php`  
  - Body (form-data vagy JSON):  
    ```json
    {
      "username": "admin_user",
      "password": "correct_password"
    }
    ```  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Sikeres admin bejelentkezés.",
      "admin_id": 1,
      "username": "admin_user"
    }
    ```

---

**2. Hibás jelszó**  
- Body:  
    ```json
    {
      "username": "admin_user",
      "password": "wrong_password"
    }
    ```  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Hibás jelszó."
    }
    ```

---

**3. Nem létező felhasználó**  
- Body:  
    ```json
    {
      "username": "ghost_user",
      "password": "any_password"
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nincs ilyen felhasználó."
    }
    ```

---

**4. Nem admin szerepkör**  
- Body:  
    ```json
    {
      "username": "normal_user",
      "password": "correct_password"
    }
    ```  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "Nincs admin jogosultság."
    }
    ```

---

**5. Inaktív felhasználó**  
- Body:  
    ```json
    {
      "username": "inactive_admin",
      "password": "correct_password"
    }
    ```  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "A felhasználó inaktív."
    }
    ```

---

**6. Hiányzó paraméterek**  
- Body:  
    ```json
    {
      "username": "admin_user"
    }
    ```  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a felhasználónév vagy jelszó."
    }
    ```

---

**7. Érvénytelen metódus (GET)**  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

**8. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input                         | Várt válasz | HTTP kód | Eredmény |
|-------------------------|------------------------------|-------------|----------|----------|
| Sikeres bejelentkezés   | helyes admin/jelszó          | success     | 200      | ok       |
| Hibás jelszó            | rossz jelszó                 | error       | 401      | ok       |
| Nem létező felhasználó  | ghost_user                   | error       | 404      | ok       |
| Nem admin szerepkör     | normal_user                  | error       | 403      | ok       |
| Inaktív felhasználó     | inactive_admin               | error       | 403      | ok       |
| Hiányzó paraméterek     | nincs password               | error       | 422      | ok       |
| Érvénytelen metódus     | GET request                  | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL                    | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---

 


----------------------------------------------- admin/logout.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `logout.php`

---

### Cél  
A `logout.php` endpoint feladata, hogy **biztonságos kijelentkezést biztosítson az adminisztrátorok számára**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ha van, törli a session változókat, a cookie‑t, és lezárja a session‑t.  
- JSON választ ad vissza: `success` ha sikeres, `error` ha nem volt aktív session.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres kijelentkezés** – aktív admin session van.  
- **Nincs aktív session** – nincs bejelentkezett admin.  
- **Érvénytelen metódus** – pl. GET.  

---

### Részletes tesztpéldák

**1. Sikeres kijelentkezés**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/logout.php`  
  - Feltétel: előtte sikeres `admin_login.php` hívás történt (`admin_user` bejelentkezve).  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Sikeres kijelentkezés."
    }
    ```

---

**2. Nincs aktív session**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/logout.php`  
  - Feltétel: nincs aktív admin session.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**3. Érvénytelen metódus (GET)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/logout.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

### Összefoglaló táblázat

| Teszt                | Input              | Várt válasz | HTTP kód | Eredmény |
|-----------------------|-------------------|-------------|----------|----------|
| Sikeres kijelentkezés | POST, aktív admin | success     | 200      | ok       |
| Nincs aktív session   | POST, nincs admin | error       | 401      | ok       |
| Érvénytelen metódus   | GET request       | error       | 405      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A táblázat gyors áttekintést ad, a részletes példák pedig tanulásra és demonstrációra is alkalmasak.  

---




----------------------------------------------- admin/admin_get_user_list.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `admin_get_user_list.php`

---

### Cél  
Az `admin_get_user_list.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes felhasználót**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja az összes felhasználót (aktív és inaktív).  
- JSON formátumban adja vissza az adatokat.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérés** – aktív admin session, minden user listázva.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. POST.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres lekérés (admin_user bejelentkezve)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/admin_get_user_list.php`  
- Response (200 OK):  
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
            "id": 3,
            "username": "user3",
            "email": "user3@example.com",
            "role": "user",
            "is_active": 1
          },
          {
            "id": 8,
            "username": "user8",
            "email": "user8@example.com",
            "role": "user",
            "is_active": 1
          },
          {
            "id": 9,
            "username": "user9",
            "email": "user9@example.com",
            "role": "user",
            "is_active": 1
          }
          // … további userek
        ]
      }
    }
    ```

---

**2. Nincs aktív admin session**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/admin_get_user_list.php`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**3. Érvénytelen metódus (POST)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/admin_get_user_list.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak GET metódus engedélyezett."
    }
    ```

---

**4. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres lekérés         | GET, aktív admin  | success     | 200      | ok       |
| Nincs aktív session     | GET, nincs admin  | error       | 401      | ok       |
| Érvénytelen metódus     | POST request      | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

## Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- A valós adatbázis minták alapján a válaszban tényleges userek jelennek meg (`user1`, `user9`, stb.), így a dokumentáció életszerű.  
- Vizsgán jól bemutatható: az admin modul első funkciója, a felhasználók áttekintése.  

---




----------------------------------------------- admin/get_user_details.php ---------------------------------------------------

### 🎯 Thunder Client tesztforgatókönyv – Admin modul / `get_user_details.php`

---

### Cél  
A `get_user_details.php` endpoint feladata, hogy **az adminisztrátor számára egy adott felhasználó részletes adatait adja vissza**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja a felhasználó alapadatait (ID, username, email, role, is_active, created_at).  
- Visszaadja a felhasználóhoz tartozó hirdetéseket.  
- Hibakezelést tartalmaz: hiányzó paraméter, nem létező user, rossz metódus.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérés** – aktív admin session, létező user ID.  
- **Nem létező user ID** – nincs ilyen felhasználó.  
- **Hiányzó paraméter** – nincs `id`.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. POST.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres lekérés (admin_user bejelentkezve, `user9`)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/get_user_details.php?id=9`  
- Response (200 OK):  
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

**2. Nem létező user ID**  
- Request:  
  - URL: `http://localhost/legora/admin/get_user_details.php?id=999`  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nem található felhasználó ezzel az ID-val."
    }
    ```

---

**3. Hiányzó paraméter**  
- Request:  
  - URL: `http://localhost/legora/admin/get_user_details.php`  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a felhasználó azonosító (id)."
    }
    ```

---

**4. Nincs aktív admin session**  
- Request:  
  - URL: `http://localhost/legora/admin/get_user_details.php?id=9`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**5. Érvénytelen metódus (POST)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/get_user_details.php?id=9`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak GET metódus engedélyezett."
    }
    ```

---

**6. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input                        | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-----------------------------|-------------|----------|----------|
| Sikeres lekérés         | GET, id=9, aktív admin      | success     | 200      | ok       |
| Nem létező user ID      | GET, id=999                 | error       | 404      | ok       |
| Hiányzó paraméter       | GET, nincs id               | error       | 422      | ok       |
| Nincs aktív session     | GET, id=9, nincs admin      | error       | 401      | ok       |
| Érvénytelen metódus     | POST, id=9                  | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL                   | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges userek és hirdetések jelennek meg (`user9`, "The Majestic Horse", "Wonder Woman minifig").  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul részletes felhasználókezelési funkciója.  

---






----------------------------------------------- admin/admin_delete_user.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `admin_delete_user.php`

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

### Lehetséges tesztek (áttekintés)
- **Sikeres törlés** – létező user, nem admin, aktív session.  
- **Nem létező user ID** – nincs ilyen felhasználó.  
- **Admin törlés tiltva** – admin role esetén hiba.  
- **Hiányzó paraméter** – nincs `id`.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. GET.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres törlés (admin_user bejelentkezve, `user9`)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/admin_delete_user.php`  
  - Body (form-data vagy JSON):  
    ```json
    {
      "id": 9
    }
    ```  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Felhasználó sikeresen inaktiválva.",
      "user_id": 9,
      "username": "user9"
    }
    ```

---

**2. Nem létező user ID**  
- Body:  
    ```json
    {
      "id": 999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nem található felhasználó ezzel az ID-val."
    }
    ```

---

**3. Admin törlés tiltva**  
- Body:  
    ```json
    {
      "id": 1
    }
    ```  
- Feltétel: `id=1` admin_user.  
- Response (403 Forbidden):  
    ```json
    {
      "status": "error",
      "message": "Admin felhasználó nem törölhető."
    }
    ```

---

**4. Hiányzó paraméter**  
- Body: üres vagy nincs `id`.  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a felhasználó azonosító (id)."
    }
    ```

---

**5. Nincs aktív admin session**  
- Request:  
  - Method: POST  
  - Body: `{ "id": 9 }`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**6. Érvénytelen metódus (GET)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/admin_delete_user.php?id=9`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres törlés          | POST, id=9        | success     | 200      | ok       |
| Nem létező user ID      | POST, id=999      | error       | 404      | ok       |
| Admin törlés tiltva     | POST, id=1        | error       | 403      | ok       |
| Hiányzó paraméter       | POST, nincs id    | error       | 422      | ok       |
| Nincs aktív session     | POST, id=9        | error       | 401      | ok       |
| Érvénytelen metódus     | GET, id=9         | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges userek jelennek meg (`user9`, `user1`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul egyik kulcsfunkciója, a felhasználók törlése/inaktiválása.  

---






----------------------------------------------- admin/admin_restore_user.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `admin_restore_user.php`

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

### Lehetséges tesztek (áttekintés)
- **Sikeres visszaállítás** – létező user, inaktív, aktív admin session.  
- **Nem létező user ID** – nincs ilyen felhasználó.  
- **Már aktív user** – nem inaktivált, így nem állítható vissza.  
- **Hiányzó paraméter** – nincs `id`.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. GET.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres visszaállítás (admin_user bejelentkezve, `user9` inaktív)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/admin_restore_user.php`  
  - Body:  
    ```json
    {
      "id": 9
    }
    ```  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Felhasználó sikeresen visszaállítva.",
      "user_id": 9,
      "username": "user9"
    }
    ```

---

**2. Nem létező user ID**  
- Body:  
    ```json
    {
      "id": 999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nem található felhasználó ezzel az ID-val."
    }
    ```

---

**3. Már aktív user (pl. `user13`)**  
- Body:  
    ```json
    {
      "id": 13
    }
    ```  
- Response (409 Conflict):  
    ```json
    {
      "status": "error",
      "message": "A felhasználó nincs inaktiválva, így nem állítható vissza."
    }
    ```

---

**4. Hiányzó paraméter**  
- Body: üres vagy nincs `id`.  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a felhasználó azonosító (id)."
    }
    ```

---

**5. Nincs aktív admin session**  
- Request:  
  - Method: POST  
  - Body: `{ "id": 9 }`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**6. Érvénytelen metódus (GET)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/admin_restore_user.php?id=9`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres visszaállítás   | POST, id=9        | success     | 200      | ok       |
| Nem létező user ID      | POST, id=999      | error       | 404      | ok       |
| Már aktív user          | POST, id=13       | error       | 409      | ok       |
| Hiányzó paraméter       | POST, nincs id    | error       | 422      | ok       |
| Nincs aktív session     | POST, id=9        | error       | 401      | ok       |
| Érvénytelen metódus     | GET, id=9         | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges userek jelennek meg (`user9`, `user13`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul teljes körű user‑kezelése (törlés + visszaállítás).  

---




----------------------------------------------- admin/toggle_user.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `toggle_user.php`

---

### Cél  
A `toggle_user.php` endpoint feladata, hogy **az adminisztrátor számára lehetővé tegye egy felhasználó aktív/inaktív státuszának váltását**.  
- Csak POST metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Ellenőrzi, hogy létezik‑e a felhasználó.  
- Ha aktív volt, inaktiválja (`is_active = 0`), ha inaktív volt, aktiválja (`is_active = 1`).  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres váltás** – létező user, aktív session.  
- **Nem létező user ID** – nincs ilyen felhasználó.  
- **Hiányzó paraméter** – nincs `id`.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. GET.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres váltás (admin_user bejelentkezve, `user9` aktív → inaktív)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/toggle_user.php`  
  - Body:  
    ```json
    {
      "id": 9
    }
    ```  
- Response (200 OK):  
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

**2. Sikeres váltás (admin_user bejelentkezve, `user13` inaktív → aktív)**  
- Request:  
  - Method: POST  
  - Body:  
    ```json
    {
      "id": 13
    }
    ```  
- Response (200 OK):  
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

**3. Nem létező user ID**  
- Body:  
    ```json
    {
      "id": 999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nem található felhasználó ezzel az ID-val."
    }
    ```

---

**4. Hiányzó paraméter**  
- Body: üres vagy nincs `id`.  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a felhasználó azonosító (id)."
    }
    ```

---

**5. Nincs aktív admin session**  
- Request:  
  - Method: POST  
  - Body: `{ "id": 9 }`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**6. Érvénytelen metódus (GET)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/toggle_user.php?id=9`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres váltás (aktív→inaktív) | POST, id=9        | success     | 200      | ok       |
| Sikeres váltás (inaktív→aktív) | POST, id=13       | success     | 200      | ok       |
| Nem létező user ID      | POST, id=999      | error       | 404      | ok       |
| Hiányzó paraméter       | POST, nincs id    | error       | 422      | ok       |
| Nincs aktív session     | POST, id=9        | error       | 401      | ok       |
| Érvénytelen metódus     | GET, id=9         | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges userek jelennek meg (`user9`, `user13`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul rugalmas user‑kezelési funkciója (aktiválás/inaktiválás).  

---




----------------------------------------------- admin/admin_get_listings_list.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `admin_get_listings_list.php`

---

### Cél  
Az `admin_get_listings_list.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes hirdetést**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Visszaadja az összes hirdetést (aktív és soft delete‑elt).  
- JSON formátumban adja vissza az adatokat.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérés** – aktív admin session, minden hirdetés listázva.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. POST.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres lekérés (admin_user bejelentkezve)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/admin_get_listings_list.php`  
- Response (200 OK):  
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
            "id": 3,
            "title": "LEGO alkatrész - 73129",
            "description": "Új, csomagolt",
            "price": 199.99,
            "user_id": 9,
            "created_at": "2025-10-15 09:45:22",
            "deleted_at": null
          },
          {
            "id": 4,
            "title": "Wonder Woman minifig",
            "description": "Használt, jó állapotban",
            "price": 2999.99,
            "user_id": 9,
            "created_at": "2025-10-16 10:30:00",
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

**2. Nincs aktív admin session**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/admin_get_listings_list.php`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**3. Érvénytelen metódus (POST)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/admin_get_listings_list.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak GET metódus engedélyezett."
    }
    ```

---

**4. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres lekérés         | GET, aktív admin  | success     | 200      | ok       |
| Nincs aktív session     | GET, nincs admin  | error       | 401      | ok       |
| Érvénytelen metódus     | POST request      | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges hirdetések jelennek meg (pl. LEGO készletek, minifigek).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési áttekintő funkciója.  

---




----------------------------------------------- admin/get_deleted_listings.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `get_deleted_listings.php`

---

### Cél  
A `get_deleted_listings.php` endpoint feladata, hogy **az adminisztrátor számára listázza az összes törölt (soft delete‑elt) hirdetést**.  
- Csak GET metódus engedélyezett.  
- Ellenőrzi, hogy van‑e aktív admin session.  
- Lekérdezi a `listings` táblát, ahol `deleted_at IS NOT NULL`.  
- JOIN‑olja a `users` táblát, hogy látszódjon a hirdető neve és emailje.  
- JSON formátumban adja vissza az adatokat.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérés** – aktív admin session, törölt hirdetések listázva.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. POST.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres lekérés (admin_user bejelentkezve)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/get_deleted_listings.php`  
- Response (200 OK):  
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

**2. Nincs aktív admin session**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/get_deleted_listings.php`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**3. Érvénytelen metódus (POST)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/get_deleted_listings.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak GET metódus engedélyezett."
    }
    ```

---

**4. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres lekérés         | GET, aktív admin  | success     | 200      | ok       |
| Nincs aktív session     | GET, nincs admin  | error       | 401      | ok       |
| Érvénytelen metódus     | POST request      | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges törölt hirdetések jelennek meg (`Batman minifig`, `LEGO Castle`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési funkciója a törölt hirdetések áttekintésére.  

---






----------------------------------------------- admin/admin_restore_listing.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `admin_restore_listing.php`

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

### Lehetséges tesztek (áttekintés)
- **Sikeres visszaállítás** – létező hirdetés, törölt, aktív admin session.  
- **Nem létező hirdetés ID** – nincs ilyen hirdetés.  
- **Már aktív hirdetés** – nincs törölve, így nem állítható vissza.  
- **Hiányzó paraméter** – nincs `id`.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. GET.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres visszaállítás (admin_user bejelentkezve, `Batman minifig` törölt)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/admin_restore_listing.php`  
  - Body:  
    ```json
    {
      "id": 5
    }
    ```  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Hirdetés sikeresen visszaállítva.",
      "listing_id": 5,
      "title": "Batman minifig"
    }
    ```

---

**2. Nem létező hirdetés ID**  
- Body:  
    ```json
    {
      "id": 999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nem található hirdetés ezzel az ID-val."
    }
    ```

---

**3. Már aktív hirdetés (pl. `LEGO City Police Station`)**  
- Body:  
    ```json
    {
      "id": 2
    }
    ```  
- Response (409 Conflict):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés nincs törölve, így nem állítható vissza."
    }
    ```

---

**4. Hiányzó paraméter**  
- Body: üres vagy nincs `id`.  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a hirdetés azonosító (id)."
    }
    ```

---

**5. Nincs aktív admin session**  
- Request:  
  - Method: POST  
  - Body: `{ "id": 5 }`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**6. Érvénytelen metódus (GET)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/admin_restore_listing.php?id=5`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres visszaállítás   | POST, id=5        | success     | 200      | ok       |
| Nem létező hirdetés ID  | POST, id=999      | error       | 404      | ok       |
| Már aktív hirdetés      | POST, id=2        | error       | 409      | ok       |
| Hiányzó paraméter       | POST, nincs id    | error       | 422      | ok       |
| Nincs aktív session     | POST, id=5        | error       | 401      | ok       |
| Érvénytelen metódus     | GET, id=5         | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges hirdetések jelennek meg (`Batman minifig`, `LEGO City Police Station`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul teljes körű hirdetéskezelése (törlés + visszaállítás).  

---






----------------------------------------------- admin/admin_delete_listing.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `admin_delete_listing.php`

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

### Lehetséges tesztek (áttekintés)
- **Sikeres törlés** – létező hirdetés, aktív session.  
- **Nem létező hirdetés ID** – nincs ilyen hirdetés.  
- **Már törölt hirdetés** – hibát ad vissza.  
- **Hiányzó paraméter** – nincs `id`.  
- **Nincs aktív admin session** – nincs bejelentkezve admin.  
- **Érvénytelen metódus** – pl. GET.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres törlés (admin_user bejelentkezve, `LEGO City Police Station`)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/admin_delete_listing.php`  
  - Body:  
    ```json
    {
      "id": 2
    }
    ```  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Hirdetés sikeresen törölve (soft delete).",
      "listing_id": 2,
      "title": "LEGO City Police Station"
    }
    ```

---

**2. Nem létező hirdetés ID**  
- Body:  
    ```json
    {
      "id": 999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nem található hirdetés ezzel az ID-val."
    }
    ```

---

**3. Már törölt hirdetés (pl. `Batman minifig`)**  
- Body:  
    ```json
    {
      "id": 5
    }
    ```  
- Response (409 Conflict):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés már törölve van."
    }
    ```

---

**4. Hiányzó paraméter**  
- Body: üres vagy nincs `id`.  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a hirdetés azonosító (id)."
    }
    ```

---

**5. Nincs aktív admin session**  
- Request:  
  - Method: POST  
  - Body: `{ "id": 2 }`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**6. Érvénytelen metódus (GET)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/admin_delete_listing.php?id=2`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres törlés          | POST, id=2        | success     | 200      | ok       |
| Nem létező hirdetés ID  | POST, id=999      | error       | 404      | ok       |
| Már törölt hirdetés     | POST, id=5        | error       | 409      | ok       |
| Hiányzó paraméter       | POST, nincs id    | error       | 422      | ok       |
| Nincs aktív session     | POST, id=2        | error       | 401      | ok       |
| Érvénytelen metódus     | GET, id=2         | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges hirdetések jelennek meg (`LEGO City Police Station`, `Batman minifig`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési funkciója a soft delete megvalósítására.  

---





----------------------------------------------- admin/get_users.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – `get_users.php`

---

### Cél  
A `get_users.php` endpoint feladata, hogy **listázza az összes felhasználót** (általános user lista, nem admin).  
- Csak GET metódus engedélyezett.  
- Nem igényel admin session.  
- Lekérdezi a `users` táblát, és visszaadja a fő adataikat: `id, username, email, role, is_active, created_at, address, phone`.  
- JSON formátumban adja vissza az adatokat.  

---

### Lehetséges tesztek (áttekintés)
- **Sikeres lekérés** – minden felhasználó listázva.  
- **Érvénytelen metódus** – pl. POST.  
- **Adatbázis hiba szimuláció** – pl. hibás SQL.  

---

### Részletes tesztpéldák (valós adatok alapján)

**1. Sikeres lekérés**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/get_users.php`  
- Response (200 OK):  
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

**2. Érvénytelen metódus (POST)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/get_users.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak GET metódus engedélyezett."
    }
    ```

---

**3. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt              | Input         | Várt válasz | HTTP kód | Eredmény |
|---------------------|--------------|-------------|----------|----------|
| Sikeres lekérés     | GET request  | success     | 200      | ok       |
| Érvénytelen metódus | POST request | error       | 405      | ok       |
| Adatbázis hiba      | hibás SQL    | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges felhasználók jelennek meg (`user9`, `user13`, `admin`).  
- A frontend fejlesztőknek látszik, hogy a végpont **robosztus és kiszámítható**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az általános user listázás REST alapelvek szerint.  

---




----------------------------------------------- admin/delete_listing.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `delete_listing.php`

---

### Cél  
Az `delete_listing.php` endpoint az **admin modulban** biztosítja, hogy az adminisztrátor soft delete művelettel törölhessen bármely hirdetést.  
- Csak POST metódus engedélyezett.  
- Admin session szükséges.  
- Ellenőrzi, hogy létezik‑e a hirdetés.  
- Ha már törölve van, hibát ad vissza.  
- Soft delete: `deleted_at = NOW()`.  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Tesztforgatókönyvek

**1. Sikeres törlés (admin bejelentkezve, pl. LEGO Castle)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/delete_listing.php`  
  - Body:  
    ```json
    {
      "id": 7
    }
    ```  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Hirdetés sikeresen törölve (soft delete).",
      "listing_id": 7,
      "title": "LEGO Castle"
    }
    ```

---

**2. Nem létező hirdetés ID**  
- Body:  
    ```json
    {
      "id": 999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nem található hirdetés ezzel az ID-val."
    }
    ```

---

**3. Már törölt hirdetés (pl. Batman minifig)**  
- Body:  
    ```json
    {
      "id": 5
    }
    ```  
- Response (409 Conflict):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés már törölve van."
    }
    ```

---

**4. Hiányzó paraméter**  
- Body: üres vagy nincs `id`.  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a hirdetés azonosító (id)."
    }
    ```

---

**5. Nincs aktív admin session**  
- Request:  
  - Method: POST  
  - Body: `{ "id": 7 }`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**6. Érvénytelen metódus (GET)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/delete_listing.php?id=7`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres törlés          | POST, id=7        | success     | 200      | ok       |
| Nem létező hirdetés ID  | POST, id=999      | error       | 404      | ok       |
| Már törölt hirdetés     | POST, id=5        | error       | 409      | ok       |
| Hiányzó paraméter       | POST, nincs id    | error       | 422      | ok       |
| Nincs aktív session     | POST, id=7        | error       | 401      | ok       |
| Érvénytelen metódus     | GET, id=7         | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges hirdetések jelennek meg (`LEGO Castle`, `Batman minifig`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési funkciója a soft delete megvalósítására.  

---




----------------------------------------------- admin/restore_listing.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `restore_listing.php`

---

### Cél  
Az `restore_listing.php` endpoint az **admin modulban** biztosítja, hogy az adminisztrátor soft delete művelettel törölt hirdetéseket visszaállíthasson.  
- Csak POST metódus engedélyezett.  
- Admin session szükséges.  
- Ellenőrzi, hogy létezik‑e a hirdetés és valóban törölt állapotban van‑e.  
- Visszaállítás: `deleted_at = NULL`.  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Tesztforgatókönyvek

**1. Sikeres visszaállítás (admin bejelentkezve, pl. Batman minifig)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/restore_listing.php`  
  - Body:  
    ```json
    {
      "id": 5
    }
    ```  
- Response (200 OK):  
    ```json
    {
      "status": "success",
      "message": "Hirdetés sikeresen visszaállítva.",
      "listing_id": 5,
      "title": "Batman minifig"
    }
    ```

---

**2. Nem létező hirdetés ID**  
- Body:  
    ```json
    {
      "id": 999
    }
    ```  
- Response (404 Not Found):  
    ```json
    {
      "status": "error",
      "message": "Nem található hirdetés ezzel az ID-val."
    }
    ```

---

**3. Nem törölt hirdetés (pl. LEGO Castle)**  
- Body:  
    ```json
    {
      "id": 7
    }
    ```  
- Response (409 Conflict):  
    ```json
    {
      "status": "error",
      "message": "A hirdetés nincs törölt állapotban, így nem állítható vissza."
    }
    ```

---

**4. Hiányzó paraméter**  
- Body: üres vagy nincs `id`.  
- Response (422 Unprocessable Entity):  
    ```json
    {
      "status": "error",
      "message": "Hiányzik a hirdetés azonosító (id)."
    }
    ```

---

**5. Nincs aktív admin session**  
- Request:  
  - Method: POST  
  - Body: `{ "id": 5 }`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**6. Érvénytelen metódus (GET)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/restore_listing.php?id=5`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak POST metódus engedélyezett."
    }
    ```

---

**7. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input              | Várt válasz | HTTP kód | Eredmény |
|-------------------------|-------------------|-------------|----------|----------|
| Sikeres visszaállítás   | POST, id=5        | success     | 200      | ok       |
| Nem létező hirdetés ID  | POST, id=999      | error       | 404      | ok       |
| Nem törölt hirdetés     | POST, id=7        | error       | 409      | ok       |
| Hiányzó paraméter       | POST, nincs id    | error       | 422      | ok       |
| Nincs aktív session     | POST, id=5        | error       | 401      | ok       |
| Érvénytelen metódus     | GET, id=5         | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL         | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges hirdetések jelennek meg (`Batman minifig`, `LEGO Castle`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul hirdetéskezelési funkciója a soft delete visszaállítására.  

---





----------------------------------------------- admin/get_stats.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `get_stats.php`

---

### Cél  
Az `get_stats.php` endpoint az **admin modulban** összesítő statisztikákat ad vissza a rendszer állapotáról.  
- Csak GET metódus engedélyezett.  
- Admin session szükséges.  
- Visszaadja az aktív/törölt hirdetések és felhasználók számát.  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Tesztforgatókönyvek

**1. Sikeres lekérés (admin bejelentkezve)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/get_stats.php`  
- Response (200 OK):  
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

**2. Nincs aktív admin session**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/get_stats.php`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**3. Érvénytelen metódus (POST)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/get_stats.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak GET metódus engedélyezett."
    }
    ```

---

**4. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input         | Várt válasz | HTTP kód | Eredmény |
|-------------------------|--------------|-------------|----------|----------|
| Sikeres lekérés         | GET request  | success     | 200      | ok       |
| Nincs aktív session     | GET request  | error       | 401      | ok       |
| Érvénytelen metódus     | POST request | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL    | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges számok jelennek meg (pl. `active_listings`, `deleted_listings`, `active_users`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul statisztikai funkciója a dashboardhoz.  

---





----------------------------------------------- admin/get_all_stats.php ---------------------------------------------------

###  Thunder Client tesztforgatókönyv – Admin modul / `get_all_stats.php`

---

### Cél  
A `get_all_stats.php` endpoint az **admin modulban** komplex statisztikákat ad vissza a rendszer állapotáról.  
- Csak GET metódus engedélyezett.  
- Admin session szükséges.  
- Visszaadja:  
  - Globális számok (aktív/törölt hirdetések, aktív/inaktív/összes user).  
  - Felhasználónkénti bontás (hirdetések száma, aktív/törölt bontásban).  
  - Hirdetésenkénti összesítés (ár statisztika: átlagár, minimum, maximum).  
- JSON választ ad vissza: `success` vagy `error`.  

---

### Tesztforgatókönyvek

**1. Sikeres lekérés (admin bejelentkezve)**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/get_all_stats.php`  
- Response (200 OK):  
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

**2. Nincs aktív admin session**  
- Request:  
  - Method: GET  
  - URL: `http://localhost/legora/admin/get_all_stats.php`  
  - Feltétel: nincs bejelentkezve admin.  
- Response (401 Unauthorized):  
    ```json
    {
      "status": "error",
      "message": "Nincs aktív admin session."
    }
    ```

---

**3. Érvénytelen metódus (POST)**  
- Request:  
  - Method: POST  
  - URL: `http://localhost/legora/admin/get_all_stats.php`  
- Response (405 Method Not Allowed):  
    ```json
    {
      "status": "error",
      "message": "Csak GET metódus engedélyezett."
    }
    ```

---

**4. Adatbázis hiba szimuláció**  
- Response (500 Internal Server Error):  
    ```json
    {
      "status": "error",
      "message": "Adatbázis hiba: [hibaüzenet]"
    }
    ```

---

### Összefoglaló táblázat

| Teszt                  | Input         | Várt válasz | HTTP kód | Eredmény |
|-------------------------|--------------|-------------|----------|----------|
| Sikeres lekérés         | GET request  | success     | 200      | ok       |
| Nincs aktív session     | GET request  | error       | 401      | ok       |
| Érvénytelen metódus     | POST request | error       | 405      | ok       |
| Adatbázis hiba          | hibás SQL    | error       | 500      | ok       |

---

##  Összegzés  
- A Thunder Client tesztek lefedik az összes tipikus és hibás esetet.  
- A valós adatbázis minták alapján a válaszban tényleges statisztikai adatok jelennek meg (`global_stats`, `user_stats`, `listing_stats`).  
- A frontend fejlesztőknek látszik, hogy a végpont **biztonságos és robusztus**, minden hibát megfelelően kezel.  
- Vizsgán jól bemutatható: az admin modul komplex statisztikai funkciója (globális + user + hirdetés szint).  

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