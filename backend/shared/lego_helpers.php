<?php
// LEGO adatlekérő segédfüggvények
// Minden modul ezt a fájlt húzza be az init.php-n keresztül

/*
 * LEGO metaadatok lekérése típus alapján
 *
 * @param PDO $pdo - adatbázis kapcsolat
 * @param string $item_type - típus (set | part | minifig)
 * @param string $item_id - azonosító (pl. set_num, part_num, fig_num)
 * @return array|null - találat esetén asszociatív tömb, különben null
 */
function getLegoData(PDO $pdo, string $item_type, string $item_id): ?array
{
    switch ($item_type) {
        case 'set':
            $q = $pdo->prepare("
                SELECT name, year, img_url, num_parts
                FROM sets
                WHERE set_num = ?
                LIMIT 1
            ");
            $q->execute([$item_id]);
            return $q->fetch(PDO::FETCH_ASSOC) ?: null;

        case 'part':
            $q = $pdo->prepare("
                SELECT p.name,
                       c.name AS color,
                       c.rgb,
                       p.part_num,
                       p.part_cat_id
                FROM parts p
                LEFT JOIN elements e ON p.part_num = e.part_num
                LEFT JOIN colors c ON e.color_id = c.id
                WHERE p.part_num = ?
                LIMIT 1
            ");
            $q->execute([$item_id]);
            return $q->fetch(PDO::FETCH_ASSOC) ?: null;

        case 'minifig':
            $q = $pdo->prepare("
                SELECT name, img_url, num_parts
                FROM minifigs
                WHERE fig_num = ?
                LIMIT 1
            ");
            $q->execute([$item_id]);
            return $q->fetch(PDO::FETCH_ASSOC) ?: null;

        default:
            // Ha ismeretlen típus érkezik
            return null;
    }
}

/* 
Példa: több LEGO elem lekérése egyszerre
@param PDO $pdo
@param string $item_type
@param array $item_ids
@return array - minden elemhez visszaadja az adatokat */


function getMultipleLegoData(PDO $pdo, string $item_type, array $item_ids): array
{
    $results = [];
    foreach ($item_ids as $id) {
        $data = getLegoData($pdo, $item_type, $id);
        if ($data !== null) {
            $results[] = $data;
        }
    }
    return $results;
}


/* 
Leírás:

Mi a lego_helpers.php lényege?

Ez a modul olyan, mint egy „LEGO adatközponti ügyfélszolgálat”.

Ha egy másik programrész (pl. API végpont, admin modul, kereső funkció) LEGO adatokat szeretne lekérni az adatbázisból, akkor nem kell saját SQL lekérdezést írnia, hanem:

csak meghívja getLegoData()-t,
és megmondja, milyen típusú LEGO elemről van szó:

* set (készlet)
* part (alkatrész)
* minifig (minifigura)

A többit ez a segédfüggvény elintézi.

Ez óriási előny, mert:

* egységes az adatlekérés logikája
* kevesebb hibalehetőség
* tisztább, átláthatóbb kód
* ha holnap módosítod az SQL-t, csak ebben az egy fájlban kell

Ez a `lego_helpers.php` modul egy segédfüggvényt tartalmaz, ami arra szolgál, hogy a backend könnyen le tudja kérdezni a LEGO-adatbázisból egy adott elemhez tartozó metaadatokat.  

###  Mit csinál a `getLegoData()` függvény?
- Paraméterek:
  - `$pdo`: az adatbázis kapcsolat (PDO objektum).
  - `$item_type`: az elem típusa (`set`, `part`, `minifig`).
  - `$item_id`: az elem azonosítója (pl. `set_num`, `part_num`, `fig_num`).

- Működés:
  - A függvény a `switch` szerkezet alapján eldönti, hogy milyen típusú LEGO-elem adatait kell lekérdezni.
  - Ha `set`: a `sets` táblából lekéri a készlet nevét, évét, kép URL-jét és alkatrészek számát.
  - Ha `part`: a `parts` táblából lekéri az alkatrész nevét, kategóriáját, valamint az `elements` és `colors` táblákhoz csatlakozva a szín nevét és RGB kódját.
  - Ha `minifig`: a `minifigs` táblából lekéri a minifig nevét, kép URL-jét és alkatrészek számát.
  - Ha nem ismert típus érkezik, `null`-t ad vissza.

- Visszatérési érték:
  - Egy asszociatív tömb (`array`) az adott elem metaadataival.
  - Ha nincs találat, `null`.

---

### Összefoglaló
A `lego_helpers.php` tehát egy központi segédfüggvény, ami:
- Egységes módon biztosítja a LEGO-adatok lekérését a különböző táblákból.  
- Megkönnyíti a backend többi modulját (pl. `listings`, `search`, `orders`), mert nem kell mindenhol külön SQL-t írni.  
- Bővíthető: később új típusokat (pl. `theme`, `element`) is hozzá lehet adni.  

---

Ez a modul gyakorlatilag a “metaadat gyűjtő” szerepet tölti be: ha a frontendnek vagy más backend funkciónak szüksége van egy LEGO-elem részletes adataira, akkor ezen a függvényen keresztül kapja meg.  


##  Összegzés 2:
Ez a `lego_helpers.php` modul:  
- **Egységesen kezeli a LEGO adatlekéréseket** (`set`, `part`, `minifig`).  
- **Biztonságos SQL**: minden lekérdezés prepared statementtel fut.  
- **Egységes visszatérési érték**: találat → asszociatív tömb, nincs találat → `null`.  
- **Újdonság**: `getMultipleLegoData()` függvény, amivel egyszerre több elem adata is lekérhető.  
- Könnyen bővíthető új típusokkal (pl. `themes`, `inventories`). 
 */