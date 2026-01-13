<?php

// Globális PDO kapcsolat (egyszer jön létre, újrahasznosítható)
function getPDO()
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=legora;charset=utf8",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            return $e->getMessage(); // kompatibilis a régi hibakezeléssel
        }
    }

    return $pdo;
}


// SELECT műveletek (adatok lekérése)
function adatokLekerese($muvelet)
{
    $pdo = getPDO();
    if (!($pdo instanceof PDO)) {
        return $pdo; // hibaüzenet stringként
    }

    try {
        $stmt = $pdo->query($muvelet);
        $eredmeny = $stmt->fetchAll();

        return $eredmeny ?: []; // ha nincs találat → üres tömb

    } catch (PDOException $e) {
        return $e->getMessage(); // kompatibilis a régi mysqli hibával
    }
}


// INSERT / UPDATE / DELETE műveletek
function adatokValtoztatasa($muvelet)
{
    $pdo = getPDO();
    if (!($pdo instanceof PDO)) {
        return $pdo; // hibaüzenet stringként 
    }

    try {
        $stmt = $pdo->prepare($muvelet);
        $stmt->execute();

        return $stmt->rowCount() > 0 ? true : false;
    } catch (PDOException $e) {
        return $e->getMessage(); // kompatibilis a régi hibakezeléssel
    }
}
