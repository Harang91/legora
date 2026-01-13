##  Bevezető 1

A LEGO nem csupán játék, hanem egy globális, moduláris rendszer, amely mögött rendkívül gazdag és strukturált adatvilág áll. A projektünk célja az volt, hogy ezt a világot digitálisan is megragadhatóvá tegyük: egy olyan relációs adatbázist terveztünk és valósítottunk meg, amely egy használt LEGO piactér működését modellezi. A rendszer lehetővé teszi, hogy a felhasználók LEGO készleteket, alkatrészeket és minifigurákat hirdessenek, vásároljanak, értékeljenek és rendeléseket kezeljenek – mindezt egy valósághű, jól strukturált adatmodell keretében.

A projekt során nemcsak az SQL és az adatmodellezés technikai alapjait mélyítettük el, hanem megtanultuk, hogyan lehet egy valós problémára skálázható, biztonságos és bővíthető adatbázis-megoldást építeni. A rendszerünk a LEGO világ hivatalos metaadataira épül, de saját piactéri logikával egészül ki, így egyszerre referencia-adatbázis és interaktív alkalmazási modell.

--------------------------------------------------

bevezető 2:

## 🎓 Bevezető a LEGORA projekthez – Vizsgabizottság előtti bemutatásra

Tisztelt Vizsgabizottság!

A projektünk címe: **LEGORA** – egy relációs adatbázisra épülő, használt LEGO piactér modellje. A célunk az volt, hogy egy olyan adatbázist tervezzünk és valósítsunk meg, amely nemcsak technikailag helyes, hanem egy valós problémára is megoldást kínál: hogyan lehet egy online LEGO piacteret működtetni, ahol a felhasználók valós LEGO készleteket, alkatrészeket és minifigurákat hirdethetnek, vásárolhatnak, értékelhetnek és rendelhetnek.

A rendszerünk két fő rétegből áll:  
- egy **statikus adatbázisból**, amely a LEGO világ hivatalos metaadatait tartalmazza – ezeket a Rebrickable.com nyílt adatforrásaiból importáltuk,  
- és egy **dinamikus piactéri modulból**, amely a felhasználói interakciókat kezeli: regisztráció, hirdetésfeladás, kosár, rendelés, értékelés.

A két réteg között nincs közvetlen SQL-szintű kapcsolat, mivel például egy hirdetésben szereplő termék lehet készlet, alkatrész vagy minifigura is – ezeket nem lehet egyetlen idegen kulccsal leképezni. Ehelyett a kapcsolatot **logikai szinten** valósítottuk meg: a backend oldalon történik az ellenőrzés, hogy a hirdetésben szereplő LEGO-azonosító valóban létezik-e a megfelelő statikus táblában. Ez a megoldás biztosítja, hogy a rendszer egyszerre legyen **rugalmas** és **megbízható**.

A dinamikus modul hat fő táblára épül: `users`, `listings`, `cart`, `orders`, `order_items`, `ratings`. Ezek szorosan összekapcsolódnak, és együtt biztosítják a piactér működését. A `users` tábla minden művelet kiindulópontja – csak regisztrált felhasználók hirdethetnek, vásárolhatnak vagy értékelhetnek. A `listings` tábla tárolja a hirdetéseket, amelyek logikailag a LEGO adatbázisra épülnek. A `cart` lehetővé teszi, hogy a felhasználók kosárba helyezzék a termékeket, az `orders` és `order_items` táblák pedig a rendelési folyamatot kezelik. A `ratings` tábla biztosítja a közösségi visszajelzést és a bizalomépítést.

A rendszer minden táblája **InnoDB** motorral működik, így támogatja a tranzakciókat és az idegen kulcsos kapcsolatokat. A dinamikus táblák között szigorú **FOREIGN KEY** kapcsolatok biztosítják az adatintegritást, míg a statikus és dinamikus réteg közötti kapcsolatot a backend validáció garantálja.

A projekt során nemcsak az SQL és az adatmodellezés technikai alapjait mélyítettük el, hanem megtanultuk, hogyan lehet egy valós rendszerben az adatbiztonságot, a rugalmasságot és a felhasználói élményt összehangolni. A LEGORA adatbázis nemcsak egy iskolai feladat, hanem egy működőképes, skálázható modell, amely akár egy valódi LEGO piactér alapjául is szolgálhatna.

-------------------------------------------------

Bevezetés 3

 Bevezető a „LEGORA” projekt bemutatásához

Tisztelt Vizsgabizottság!
Engedjék meg, hogy bemutassuk a „LEGORA” nevű projektünket, amely egy valósághű, relációs adatbázisra épülő webes piactérmodell, kifejezetten LEGO termékek – készletek, alkatrészek és minifigurák – adásvételére. A projekt célja nem csupán egy adatbázis létrehozása volt, hanem egy olyan rendszer megtervezése, amely a valós világban is működőképes lenne, és amelyben a felhasználók biztonságosan és hatékonyan tudnának LEGO termékeket hirdetni, vásárolni, értékelni és nyomon követni.
A rendszer alapját egy kétpólusú adatmodell képezi: egyrészt egy statikus, külső forrásból – a Rebrickable.com nyilvános LEGO-adatbázisából – származó, strukturált LEGO metaadatbázis, másrészt egy dinamikus, felhasználói interakciókra épülő piactéri modul. A statikus réteg tartalmazza a LEGO készletek, alkatrészek, színek, témák és minifigurák részletes adatait, míg a dinamikus réteg biztosítja a felhasználók regisztrációját, a hirdetések kezelését, a kosár és rendelési folyamat működését, valamint az értékelések rögzítését.
Külön figyelmet fordítottunk arra, hogy a két réteg között – bár SQL-szinten nincs közvetlen idegen kulcsos kapcsolat – mégis szoros logikai integráció valósuljon meg. A hirdetésekben szereplő termékek azonosítói kizárólag a statikus LEGO-adatbázisban szereplő elemekre utalhatnak. Ezt nem adatbázis-szinten, hanem a backend oldali validációval és API-integrációval biztosítjuk, így garantálva, hogy a felhasználók csak valódi LEGO elemeket hirdethessenek meg. Ez a megoldás egyszerre biztosítja a rendszer rugalmasságát és az adatok hitelességét.
A projekt során különös hangsúlyt fektettünk az adatintegritásra, a relációs szemlélet következetes alkalmazására, valamint a skálázhatóságra. Az adatbázis minden táblája InnoDB motorral működik, amely lehetővé teszi a tranzakciókezelést és a FOREIGN KEY kapcsolatok használatát. A dinamikus táblák között szigorú idegen kulcsos kapcsolatok biztosítják, hogy minden hirdetés, rendelés, értékelés és kosárbejegyzés csak létező felhasználókhoz és termékekhez kapcsolódhasson.
A rendszer működését úgy terveztük meg, hogy az a valós piacterek logikáját kövesse: a felhasználók regisztrálnak, hirdetéseket adnak fel, mások ezeket kosárba helyezhetik, rendelést indíthatnak, majd a rendelés státuszát követhetik, végül pedig értékelhetik egymást. A kapcsolatok logikusan épülnek egymásra, és minden adatmozgás nyomon követhető, visszakereshető.
Összességében a LEGORA projekt nemcsak egy adatbázis, hanem egy komplex, valósághű modell, amely bemutatja, hogyan lehet egy nyílt forráskódú adatbázisra építve egy működőképes, biztonságos és skálázható piactéri rendszert létrehozni. A projekt során nemcsak az SQL és az adatmodellezés technikai aspektusait sajátítottuk el, hanem betekintést nyertünk a webes rendszerek működésébe, az adatvalidáció és integráció kihívásaiba, valamint a felhasználói élmény és adatbiztonság egyensúlyának fontosságába is.
Köszönjük a figyelmet, és örömmel válaszolunk a felmerülő kérdésekre

--------------------------------------------------------------------------------


## 🧱 Általános leírás az adatbázisról

A LEGORA adatbázis két fő rétegből áll:

1. **Statikus réteg** – Ez tartalmazza a LEGO univerzum hivatalos metaadatait, amelyeket a Rebrickable.com nyilvános adatbázisából töltöttünk le. Ezek az adatok CSV formátumban érkeztek, és többek között lefedik a LEGO készleteket (`sets.csv`), alkatrészeket (`parts.csv`), minifigurákat (`minifigs.csv`), színeket (`colors.csv`), témákat (`themes.csv`), valamint a készletek összetevőit (`inventory_parts.csv`, `inventory_minifigs.csv`, stb.). Ezek a táblák olvasható, referenciajellegű adatokat tartalmaznak, amelyeket a felhasználók nem módosíthatnak.

2. **Dinamikus réteg** – Ez a rész kezeli a felhasználói interakciókat: regisztráció, hirdetésfeladás, kosár, rendelés, értékelés. A főbb táblák: `users`, `listings`, `cart`, `orders`, `order_items`, `order_status_history`, `ratings`. Ezek a táblák egymással szorosan összekapcsolódnak, és minden adatmozgás nyomon követhető bennük. A rendszer úgy lett kialakítva, hogy minden hirdetés, rendelés és értékelés csak létező felhasználókhoz és valós LEGO elemekhez kapcsolódhasson.

A két réteg között nincs közvetlen SQL-szintű kapcsolat, mivel például a `listings.item_id` mező többféle típusra (készlet, alkatrész, minifigura) is utalhat. Ehelyett a kapcsolatot a backend oldali logika biztosítja: a rendszer csak akkor enged hirdetést létrehozni, ha az adott LEGO-azonosító valóban létezik a megfelelő statikus táblában. Ez a megoldás biztosítja a rugalmasságot és a logikai integritást egyszerre.

Az adatbázis minden táblája InnoDB motorral működik, így támogatja a tranzakciókat és a FOREIGN KEY kapcsolatok használatát. A dinamikus táblák között 24 idegen kulcsos kapcsolat biztosítja az adatintegritást, míg a statikus réteg strukturált, jól normalizált formában tárolja a LEGO világ adatait.


------------------------------------------------

## 📥 Az adatbázis forrása – Rebrickable nyers adatfájlok

A LEGORA adatbázis statikus részének alapját a [Rebrickable.com](https://rebrickable.com) weboldal nyilvánosan elérhető LEGO-adatbázisa képezi. A Rebrickable egy közösségi LEGO-adatbázis, amely részletes információkat tartalmaz a LEGO készletekről, alkatrészekről, minifigurákról, színekről, témákról és azok kapcsolatairól. A projekt során innen töltöttük le a szükséges nyers adatfájlokat CSV formátumban.

A letöltött fájlok a következők voltak:

- `themes.csv` – LEGO témák (pl. Star Wars, Technic, City)
- `colors.csv` – Színek RGB kóddal, átlátszósággal, előfordulási adatokkal
- `part_categories.csv` – Alkatrészkategóriák (pl. kerekek, ablakok)
- `parts.csv` – Egyedi LEGO alkatrészek adatai
- `part_relationships.csv` – Alkatrészek közötti kapcsolatok (pl. alternatívák)
- `elements.csv` – Alkatrész + szín kombinációk (pl. piros 2x4-es kocka)
- `sets.csv` – LEGO készletek adatai (név, év, téma, alkatrészek száma)
- `minifigs.csv` – LEGO minifigurák adatai
- `inventories.csv` – Készletverziók (egy adott készlet többféle kiadása)
- `inventory_parts.csv` – Készletekhez tartozó alkatrészek
- `inventory_sets.csv` – Készletekhez tartozó más készletek (pl. al-készletek)
- `inventory_minifigs.csv` – Készletekhez tartozó minifigurák

Ezeket a fájlokat manuálisan töltöttük le, majd előkészítettük és importáltuk a saját adatbázisunkba a **XAMPP / phpMyAdmin** környezet segítségével. A folyamat során számos technikai kihívással szembesültünk, például karakterkódolási problémákkal, adattípus-eltérésekkel és kulcsütközésekkel, de ezeket sikeresen megoldottuk.

A Rebrickable adatfájlok strukturált, jól dokumentált formában állnak rendelkezésre, így ideális alapot nyújtottak egy relációs adatbázis felépítéséhez. A fájlok tartalma közvetlenül leképezhető volt SQL-táblákra, és ezek képezik a LEGORA adatmodell statikus, referenciaértékű részét.

---


--------------------------------------------------

## Statikus és dinamikus táblák kapcsolata – logikai integráció

Az adatbázis két fő rétegre osztható:

- **Statikus táblák**: a LEGO metaadatokat tartalmazzák (pl. `sets`, `parts`, `minifigs`, `colors`, `themes` stb.), amelyek a Rebrickable nyilvános adatbázisából származnak.
- **Dinamikus táblák**: a felhasználói piactér működését biztosítják (pl. `users`, `listings`, `cart`, `orders`, `ratings` stb.).

### Nincs közvetlen SQL-kapcsolat

A két réteg között **nincs közvetlen SQL FOREIGN KEY kapcsolat**, mivel például a `listings.item_id` mező többféle típusra (készlet, alkatrész, minifigura) is utalhat. Ezért nem lehet egyetlen idegen kulccsal leképezni a kapcsolatot.

### Logikai kapcsolat és backend validáció

A kapcsolatot **logikai szinten** biztosítjuk:

- A `listings.item_type` mező (`'set'`, `'part'`, `'minifig'`) határozza meg, hogy az `item_id` melyik statikus táblára utal.
- A backend oldali kód (pl. API vagy adminfelület) ellenőrzi, hogy az `item_id` valóban létezik a megfelelő táblában.
- Így a felhasználók **csak valós LEGO elemeket** hirdethetnek meg.

Ez a megközelítés:

- **rugalmas**: nem korlátoz SQL-szintű kapcsolatokkal,
- **biztonságos**: a logikai integritást a backend garantálja,
- **bővíthető**: új típusok (pl. kiegészítők) is könnyen hozzáadhatók.

### Példa:

| listings.item_type | listings.item_id | Hivatkozott tábla |
|--------------------|------------------|-------------------|
| `'set'`            | `75257-1`        | `sets.set_num` |
| `'part'`           | `3001`           | `parts.part_num` |
| `'minifig'`        | `sw001`          | `minifigs.fig_num` |

---

Ez a frissített szemlélet tökéletesen illeszkedik a modern webalkalmazások architektúrájába, ahol az adatbázis és az alkalmazáslogika közösen biztosítják az adatok helyességét. 


---------------------------------------------------------

# A LEGORA Adatbázis Működése – Nagyon Részletes Leírás

## 1. 🔄 Adatbázis rétegei: statikus vs. dinamikus

A `legora` adatbázis két fő rétegre oszlik:


Réteg: **Statikus** 
Tartalom: LEGO metaadatok (készletek, alkatrészek, minifigurák, színek, témák)
Forrás: Rebrickable CSV fájlok
Működés: Csak olvasható, nem módosítják a felhasználók

Réteg: **Dinamikus** 
Tartalom: Felhasználói fiókok, hirdetések, kosár, rendelések, értékelések
Forrás: Webalkalmazás felhasználói
Működés: Írás/olvasás, CRUD műveletek, tranzakciók


A két réteg **logikailag kapcsolódik**, de **SQL-szinten nem**. A kapcsolatot a backend biztosítja, például:

- A `listings.item_type` + `item_id` mező alapján a rendszer ellenőrzi, hogy a hirdetett elem valóban létezik a megfelelő statikus táblában (`sets`, `parts`, `minifigs`).
- Ez a validáció nem SQL FOREIGN KEY, hanem alkalmazáslogikai szinten történik.

---

## 2. Felhasználók és jogosultságok (`users`)

A `users` tábla minden dinamikus adat kiindulópontja. Minden felhasználónak van:

- egyedi azonosítója (`id`),
- felhasználóneve, e-mail címe, jelszó hash-e,
- regisztrációs időpontja,
- szerepköre (`user` vagy `admin`),
- opcionális címe és telefonszáma.

Ez a tábla kapcsolódik:

- `listings` (hirdetések),
- `cart` (kosár),
- `orders` (mint vásárló és eladó is),
- `ratings` (értékelő és értékelt),
- `order_status_history` (státuszváltás végrehajtója).

---

## 3. Hirdetések (`listings`)

A `listings` tábla a piactér szíve. Minden hirdetés tartalmazza:

- a hirdető felhasználó azonosítóját (`user_id`),
- az eladásra kínált elem típusát (`item_type`: `'set'`, `'part'`, `'minifig'`),
- az elem azonosítóját (`item_id`), amely logikailag a statikus táblákra utal,
- a mennyiséget, árat, állapotot (`new` vagy `used`),
- opcionális leírást és képet.

A `listings` tábla kapcsolódik:

- `users` (ki hirdette meg),
- `cart` (mely felhasználók tették kosárba),
- `order_items` (mely rendelések tartalmazzák).

---

## 4. Kosár (`cart`)

A `cart` tábla a felhasználók ideiglenes vásárlási szándékát tárolja. Minden rekord:

- egy felhasználóhoz (`user_id`) és
- egy hirdetéshez (`listing_id`) tartozik,
- tartalmazza a mennyiséget és az időbélyeget (`added_at`).

Ez lehetővé teszi, hogy minden felhasználó saját kosarat építsen fel.

---

## 5.Rendelések (`orders`, `order_items`, `order_status_history`)

### 5.1. `orders` – rendelés fejléce

Tartalmazza:

- a vásárló és az eladó azonosítóját (`buyer_id`, `seller_id`),
- a teljes árat,
- a rendelés státuszát (`pending`, `paid`, `shipped`, `completed`),
- az időbélyeget.

### 5.2. `order_items` – rendelés tételei

Lehetővé teszi, hogy egy rendelés több hirdetést is tartalmazzon. Minden tétel:

- egy rendeléshez (`order_id`) és
- egy hirdetéshez (`listing_id`) tartozik,
- tartalmazza a mennyiséget és az árat a rendelés pillanatában.

### 5.3. `order_status_history` – státuszváltások naplózása

Minden rekord:

- egy rendeléshez tartozik (`order_id`),
- tartalmazza a régi és új státuszt,
- a módosító felhasználó azonosítóját (`changed_by`),
- és az időbélyeget.

Ez lehetővé teszi a rendelés életciklusának teljes nyomon követését.

---

## 6. Értékelések (`ratings`)

A `ratings` tábla a felhasználók közötti bizalomépítést szolgálja. Minden értékelés:

- egy értékelőtől (`rater_id`) egy másik felhasználónak (`rated_user_id`) szól,
- tartalmaz egy 1–5 közötti pontszámot (`rating`),
- opcionális szöveges kommentet,
- és az értékelés időpontját.

Ez a tábla kétszeresen kapcsolódik a `users` táblához.

---

## 7. LEGO metaadatok (statikus táblák)

### 7.1. `sets`, `parts`, `minifigs`

- A LEGO készletek, alkatrészek és minifigurák alapadatait tartalmazzák.
- Mindegyik rendelkezik egyedi azonosítóval (`set_num`, `part_num`, `fig_num`), névvel, képpel, évszámmal stb.

### 7.2. `colors`, `themes`, `part_categories`

- A `colors` tábla tartalmazza a színeket (RGB, átlátszóság, évek).
- A `themes` tábla a LEGO témákat (pl. Star Wars, City).
- A `part_categories` az alkatrészek kategóriáit (pl. kerekek, ablakok).

### 7.3. `inventories`, `inventory_parts`, `inventory_minifigs`, `inventory_sets`

- A `sets` készletekhez tartozó összetevőket írják le.
- Egy `inventory` egy adott készlet egy verzióját jelenti.
- Az `inventory_parts` és `inventory_minifigs` táblák kapcsolótáblák, amelyek megmondják, milyen alkatrészek és minifigurák tartoznak egy készlethez.
- Az `inventory_sets` lehetővé teszi, hogy egy készlet más készleteket is tartalmazzon.

### 7.4. `elements`, `part_relationships`

- Az `elements` tábla egy alkatrész + szín kombinációt ír le.
- A `part_relationships` tábla például alternatív vagy helyettesítő alkatrészeket kapcsol össze.

---

## 8. Adatintegritás és adatkezelés

- Az adatbázis **InnoDB** motort használ, amely támogatja a tranzakciókat és a **FOREIGN KEY** kapcsolatokat.
- A dinamikus táblák között mindenhol **szigorú idegen kulcsos kapcsolatok** vannak, amelyek megakadályozzák az árva rekordokat.
- A statikus és dinamikus táblák között **nincs SQL-szintű kapcsolat**, de a backend biztosítja a **logikai integritást**.

---

## 9. Adatfolyam – Egy vásárlás teljes útja

1. A felhasználó regisztrál a `users` táblába.
2. Hirdetést ad fel a `listings` táblában (pl. egy LEGO készletet).
3. Egy másik felhasználó kosárba teszi a hirdetést (`cart`).
4. A kosárból rendelést indít (`orders`), amelyhez rendelési tételek (`order_items`) tartoznak.
5. A rendelés státusza változik (`order_status_history`).
6. A vásárló értékeli az eladót (`ratings`).

-------------------------------------------------------------------------


#  Kapcsolattípusok az adatbázisban – Nagyon részletes leírás

##  Szóbeli összefoglaló – Kapcsolattípusok a LEGORA adatbázisban

A LEGORA adatbázisban a táblák közötti kapcsolatok kulcsszerepet játszanak abban, hogy a rendszer logikusan, konzisztensen és megbízhatóan működjön. Három fő kapcsolattípust alkalmaztunk: egy-a-sokhoz, több-a-többhöz és logikai kapcsolatok. Ezeket szeretném most röviden bemutatni.

### 1. Egy-a-sokhoz kapcsolatok

Ez a leggyakoribb típus. Azt jelenti, hogy például egy felhasználó több hirdetést is feladhat, de egy hirdetés csak egy felhasználóhoz tartozhat. Ugyanez igaz a rendelésekre is: egy rendeléshez több tétel tartozhat, de minden tétel csak egy rendeléshez kapcsolódik. Ezeket SQL-szinten idegen kulcsokkal valósítottuk meg, például a `listings.user_id` mező a `users.id` mezőre hivatkozik.

### 2. Több-a-többhöz kapcsolatok

Itt már szükség van egy kapcsolótáblára. Például egy rendelés több hirdetést is tartalmazhat, és egy hirdetés is szerepelhet több rendelésben – ezt a `order_items` tábla oldja fel. Ugyanez a logika működik a LEGO készletek és alkatrészek között is: egy készlet többféle alkatrészt tartalmazhat, és egy alkatrész több készletben is előfordulhat – ezt az `inventory_parts` tábla kezeli.

### 3. Logikai kapcsolatok

Ezek azok a kapcsolatok, amelyek nem SQL-szinten, hanem a backend oldalon valósulnak meg. Például a `listings` tábla `item_id` mezője utalhat készletre, alkatrészre vagy minifigurára is – de mivel ez három különböző tábla, nem lehet rá idegen kulcsot tenni. Ehelyett a rendszer ellenőrzi, hogy az adott azonosító valóban létezik-e a megadott típusú táblában. Ugyanez igaz az `inventories.set_num` mezőre is, ami szintén vegyesen tartalmazhat készleteket és minifigurákat.

### 4. Összegzés

Összesen 24 SQL-szintű idegen kulcsot definiáltunk, amelyek biztosítják az adatok közötti kapcsolatokat és az integritást. Emellett több logikai kapcsolatot is kezeltünk programozott validációval. Így az adatbázisunk nemcsak technikailag korrekt, hanem valósághűen modellezi egy LEGO piactér működését is.

---

## 1. Egy-a-sokhoz kapcsolat (1:N)

Ez a leggyakoribb kapcsolattípus az adatbázisban. Azt jelenti, hogy egy rekord az egyik táblában több rekordhoz kapcsolódhat egy másik táblában, de fordítva nem.

### Példák:

#### 🔹 `users` → `listings`
- Egy felhasználó több hirdetést is feladhat.
- A `listings.user_id` mező idegen kulcsként hivatkozik a `users.id` mezőre.

#### 🔹 `sets` → `inventories`
- Egy LEGO készlethez több készletverzió (`inventory`) is tartozhat.
- A `inventories.set_num` mező logikailag a `sets.set_num` mezőre utal (de nincs SQL FK, mert lehet minifigura is).

#### 🔹 `inventories` → `inventory_parts`, `inventory_minifigs`, `inventory_sets`
- Egy készletverzió többféle alkatrészt, minifigurát vagy más készletet is tartalmazhat.
- Ezek a kapcsolatok SQL-szinten is megvalósulnak idegen kulcsokkal.

#### 🔹 `orders` → `order_items`
- Egy rendelés több tételt tartalmazhat.
- A `order_items.order_id` mező idegen kulcs az `orders.id` mezőre.

#### 🔹 `orders` → `order_status_history`
- Egy rendelés státusza többször is változhat.
- A `order_status_history.order_id` mező idegen kulcs az `orders.id` mezőre.

---

## 2. Több-a-többhöz kapcsolat (N:M)

Ez a kapcsolat akkor fordul elő, amikor két tábla között többes kapcsolat van: egy rekord több másikhoz is kapcsolódhat, és fordítva is. Ezt mindig egy **kapcsolótábla** segítségével valósítjuk meg.

### Példák:

#### 🔹 `orders` ↔ `listings` (kapcsolótábla: `order_items`)
- Egy rendelés több hirdetést is tartalmazhat.
- Egy hirdetés több rendelésben is szerepelhet (pl. ha többször vásárolják meg).
- A `order_items` tábla oldja fel ezt a kapcsolatot, és tartalmazza a mennyiséget, árat is.

#### 🔹 `inventories` ↔ `parts` (kapcsolótábla: `inventory_parts`)
- Egy készletverzió többféle alkatrészt tartalmazhat.
- Egy alkatrész több készletben is előfordulhat.
- A `inventory_parts` tábla tartalmazza a mennyiséget, színt, és hogy extra-e.

#### 🔹 `inventories` ↔ `minifigs` (kapcsolótábla: `inventory_minifigs`)
- Ugyanaz az elv, mint az alkatrészeknél.

#### 🔹 `inventories` ↔ `sets` (kapcsolótábla: `inventory_sets`)
- Egy készlet tartalmazhat más készleteket is (pl. bónusz csomagként).

---
## 3. Egy-az-egyhez kapcsolat (1:1)

Ez ritkább, de speciális esetekben előfordulhat. Azt jelenti, hogy egy rekord csak egy másik rekordhoz kapcsolódhat, és fordítva is.

### Példák:

#### 🔹 `users` ↔ `ratings` (egy adott értékelés)
- Egy adott értékelésben egy értékelő és egy értékelt felhasználó szerepel.
- Bár egy felhasználó több értékelést is adhat vagy kaphat, egy adott értékelés csak egy párra vonatkozik.
- Ezért a `ratings` tábla sorai 1:1 kapcsolatot jelentenek az adott értékelő–értékelt párok között.

#### 🔹 `elements` ↔ `parts` + `colors`
- Egy `element` egyetlen `part_num` és `color_id` kombináció.
- Bár technikailag ez 1:N kapcsolat is lehetne, a `elements` tábla minden sora egyedi kombináció, így logikailag 1:1.

---

## 4. Logikai kapcsolatok (nem SQL-szintű, de fontos)

Ezek a kapcsolatok nem valósulnak meg SQL FOREIGN KEY formájában, de a rendszer működése szempontjából kulcsfontosságúak. A backend oldali logika biztosítja őket.

### Példák:

#### 🔹 `listings.item_type` + `item_id` → `sets`, `parts`, `minifigs`
- A `listings` tábla nem tud SQL-szinten hivatkozni három különböző táblára.
- Ehelyett a backend ellenőrzi, hogy az `item_id` valóban létezik-e a megadott típusú táblában.

#### 🔹 `inventories.set_num` → `sets.set_num` vagy `minifigs.fig_num`
- Az `inventories` tábla `set_num` mezője vegyesen tartalmazhat készleteket és minifigurákat.
- Ezért nincs SQL FK, de a logikai kapcsolat fennáll.

---

## 5. FOREIGN KEY kapcsolatok összesítve

A dinamikus táblák között **24 darab SQL FOREIGN KEY kapcsolat** biztosítja az adatintegritást. Ezek közül néhány:

- `listings.user_id` → `users.id`
- `cart.user_id` → `users.id`
- `cart.listing_id` → `listings.id`
- `orders.buyer_id` / `seller_id` → `users.id`
- `order_items.order_id` → `orders.id`
- `order_items.listing_id` → `listings.id`
- `ratings.rater_id` / `rated_user_id` → `users.id`

A statikus táblák között további 13 kulcs biztosítja a LEGO metaadatok konzisztenciáját (pl. `parts.part_cat_id` → `part_categories.id`).

---

## Összefoglalás

| Kapcsolattípus       | Példa táblák között                       | Megvalósítás |
|----------------------|-------------------------------------------|--------------|
| Egy-a-sokhoz (1:N)   | `users` → `listings`                      | SQL FK       |
| Több-a-többhöz (N:M) | `orders` ↔ `listings` (via `order_items`) | Kapcsolótábla |
| Egy-az-egyhez (1:1)  | `ratings` (egy értékelés = egy pár)       | Logikai      |
| Logikai kapcsolat    | `listings.item_id` → statikus táblák      | Backend validáció |
| Vegyes kapcsolat     | `inventories.set_num` → több tábla        | Nincs FK     |

---


------------------------------------------------------------------------

##  Teljes FOREIGN KEY script – frissítve 2026.01.13.


 A relációs integritást **24 FOREIGN KEY kapcsolattal** biztosítottam. A kapcsolatok logikusan épülnek a táblák közötti hierarchiára: témák, alkatrészek, színek, készletek, minifigurák, felhasználók, hirdetések és rendelések. A statikus és dinamikus rétegek között nincs közvetlen SQL-kapcsolat, de a `listings.item_id` mező logikailag a LEGO metaadatokra utal. Az `inventories.set_num` mezőre nem definiáltam idegen kulcsot, mivel az vegyesen tartalmaz készleteket és figurákat – ezt a Rebrickable hivatalos adatmodellje is így kezeli.

A sorrend logikusan épül fel, hogy elkerülje a hivatkozási hibákat. A dinamikus és statikus táblák közötti kapcsolat továbbra is logikai szinten történik (pl. `listings.item_id`), így ezekhez nem tartozik SQL-szintű kulcs.

---

### 🔹 1. sets.theme_id → themes.id
```sql
ALTER TABLE sets
ADD CONSTRAINT fk_sets_theme
FOREIGN KEY (theme_id) REFERENCES themes(id);
```

### 🔹 2. parts.part_cat_id → part_categories.id
```sql
ALTER TABLE parts
ADD CONSTRAINT fk_parts_category
FOREIGN KEY (part_cat_id) REFERENCES part_categories(id);
```

### 🔹 3. inventory_parts.inventory_id → inventories.id
```sql
ALTER TABLE inventory_parts
ADD CONSTRAINT fk_invparts_inventory
FOREIGN KEY (inventory_id) REFERENCES inventories(id);
```

### 🔹 4. inventory_parts.part_num → parts.part_num
```sql
ALTER TABLE inventory_parts
ADD CONSTRAINT fk_invparts_part
FOREIGN KEY (part_num) REFERENCES parts(part_num);
```

### 🔹 5. inventory_parts.color_id → colors.id
```sql
ALTER TABLE inventory_parts
ADD CONSTRAINT fk_invparts_color
FOREIGN KEY (color_id) REFERENCES colors(id);
```

### 🔹 6. inventory_minifigs.inventory_id → inventories.id
```sql
ALTER TABLE inventory_minifigs
ADD CONSTRAINT fk_invminifigs_inventory
FOREIGN KEY (inventory_id) REFERENCES inventories(id);
```

### 🔹 7. inventory_minifigs.fig_num → minifigs.fig_num
```sql
ALTER TABLE inventory_minifigs
ADD CONSTRAINT fk_invminifigs_fig
FOREIGN KEY (fig_num) REFERENCES minifigs(fig_num);
```

### 🔹 8. inventory_sets.inventory_id → inventories.id
```sql
ALTER TABLE inventory_sets
ADD CONSTRAINT fk_invsets_inventory
FOREIGN KEY (inventory_id) REFERENCES inventories(id);
```

### 🔹 9. inventory_sets.set_num → sets.set_num
```sql
ALTER TABLE inventory_sets
ADD CONSTRAINT fk_invsets_set
FOREIGN KEY (set_num) REFERENCES sets(set_num);
```

### 🔹 10. elements.part_num → parts.part_num
```sql
ALTER TABLE elements
ADD CONSTRAINT fk_elements_part
FOREIGN KEY (part_num) REFERENCES parts(part_num);
```

### 🔹 11. elements.color_id → colors.id
```sql
ALTER TABLE elements
ADD CONSTRAINT fk_elements_color
FOREIGN KEY (color_id) REFERENCES colors(id);
```

### 🔹 12. part_relationships.child_part_num → parts.part_num
```sql
ALTER TABLE part_relationships
ADD CONSTRAINT fk_partrels_child
FOREIGN KEY (child_part_num) REFERENCES parts(part_num);
```

### 🔹 13. part_relationships.parent_part_num → parts.part_num
```sql
ALTER TABLE part_relationships
ADD CONSTRAINT fk_partrels_parent
FOREIGN KEY (parent_part_num) REFERENCES parts(part_num);
```

---

## 🧩 Dinamikus piactér – új FK kapcsolatok

### 🔹 14. listings.user_id → users.id
```sql
ALTER TABLE listings
ADD CONSTRAINT fk_listings_user
FOREIGN KEY (user_id) REFERENCES users(id);
```

### 🔹 15. cart.user_id → users.id
```sql
ALTER TABLE cart
ADD CONSTRAINT fk_cart_user
FOREIGN KEY (user_id) REFERENCES users(id);
```

### 🔹 16. cart.listing_id → listings.id
```sql
ALTER TABLE cart
ADD CONSTRAINT fk_cart_listing
FOREIGN KEY (listing_id) REFERENCES listings(id);
```

### 🔹 17. orders.buyer_id → users.id
```sql
ALTER TABLE orders
ADD CONSTRAINT fk_orders_buyer
FOREIGN KEY (buyer_id) REFERENCES users(id);
```

### 🔹 18. orders.seller_id → users.id
```sql
ALTER TABLE orders
ADD CONSTRAINT fk_orders_seller
FOREIGN KEY (seller_id) REFERENCES users(id);
```

### 🔹 19. order_items.order_id → orders.id
```sql
ALTER TABLE order_items
ADD CONSTRAINT fk_orderitems_order
FOREIGN KEY (order_id) REFERENCES orders(id);
```

### 🔹 20. order_items.listing_id → listings.id
```sql
ALTER TABLE order_items
ADD CONSTRAINT fk_orderitems_listing
FOREIGN KEY (listing_id) REFERENCES listings(id);
```

### 🔹 21. order_status_history.order_id → orders.id
```sql
ALTER TABLE order_status_history
ADD CONSTRAINT fk_orderstatus_order
FOREIGN KEY (order_id) REFERENCES orders(id);
```

### 🔹 22. order_status_history.changed_by → users.id
```sql
ALTER TABLE order_status_history
ADD CONSTRAINT fk_orderstatus_user
FOREIGN KEY (changed_by) REFERENCES users(id);
```

### 🔹 23. ratings.rater_id → users.id
```sql
ALTER TABLE ratings
ADD CONSTRAINT fk_ratings_rater
FOREIGN KEY (rater_id) REFERENCES users(id);
```

### 🔹 24. ratings.rated_user_id → users.id
```sql
ALTER TABLE ratings
ADD CONSTRAINT fk_ratings_rated
FOREIGN KEY (rated_user_id) REFERENCES users(id);
```

---

## inventories.set_num mező – továbbra sincs FK

- Az `inventories.set_num` mező vegyesen tartalmazhat készleteket és minifigurákat.
- Ezért **nem lehet rá FOREIGN KEY-et tenni**, mert nem egyetlen táblára mutat.
- A Rebrickable hivatalos sémája sem használ rá kulcsot.



---------------------------------------------------------------------

## Összegzés – A LEGORA projekt lezárása

A LEGORA projekt során egy olyan relációs adatbázist hoztunk létre, amely nemcsak technikailag korrekt, hanem egy valós élethelyzetet is modellez: egy használt LEGO piactér működését. A rendszerünk két fő rétegre épül – egy statikus, referenciaadatokat tartalmazó LEGO metaadatbázisra, valamint egy dinamikus, felhasználói interakciókat kezelő piactéri modulra. A két réteg közötti kapcsolatot nem SQL-szinten, hanem backend oldali validációval biztosítottuk, így egyszerre értük el a rugalmasságot és az adatintegritást.

A projekt során:

- **12 különböző Rebrickable-adatfájlt** dolgoztunk fel, és alakítottunk át SQL-kompatibilis táblákká.
- **24 idegen kulcsos kapcsolatot** definiáltunk a dinamikus táblák között, amelyek biztosítják az adatok konzisztenciáját.
- A statikus rétegben **13 további FOREIGN KEY kapcsolatot** hoztunk létre, amelyek a LEGO metaadatok közötti logikai összefüggéseket tükrözik.
- A rendszer minden táblája **InnoDB motorral** működik, így támogatja a tranzakciókat és a relációs integritást.
- A kapcsolattípusokat tudatosan választottuk meg: egy-a-sokhoz, több-a-többhöz és logikai kapcsolatok egyaránt szerepelnek a modellben.

A projekt során nemcsak technikai tudásunkat mélyítettük el az SQL, az adatmodellezés és az adatimportálás terén, hanem megtapasztaltuk, milyen kihívásokkal jár egy valósághű rendszer felépítése: karakterkódolási problémák, kulcsütközések, adattípus-eltérések, valamint a logikai és fizikai adatkapcsolatok összehangolása.

A LEGORA adatbázis nem csupán egy iskolai feladat, hanem egy működőképes, skálázható modell, amely akár egy valódi webalkalmazás alapjául is szolgálhatna. A projekt során szerzett tapasztalataink megerősítettek bennünket abban, hogy a relációs adatmodellezés nemcsak elméleti tudás, hanem egy olyan eszköz, amellyel komplex, valós problémákra is hatékony megoldásokat lehet építeni.

---


------------------------------------The End ---------------------------------------------