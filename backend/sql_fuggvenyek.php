<?php
    //Adatok lekérése az adatbázisból:
    function adatokLekerese($muvelet) {
        $db = new mysqli("localhost", "root", "", "legora");
        if ($db->connect_errno != 0) {
            return $db->connect_error;
        }
        $eredmeny = $db->query($muvelet);

        if ($db->errno != 0) {
            return $db->error;
        }

        return ($eredmeny->num_rows > 0) ? $eredmeny->fetch_all(MYSQLI_ASSOC) : [] ;
    }


    //INSERT, UPDATE, DELETE típusú SQL műveletekhez:
    function adatokValtoztatasa($muvelet){
        $db = new mysqli("localhost", "root", "", "legora");
        if ($db->connect_errno != 0) {
            return $db->connect_error;
        }

        $db->query($muvelet);

        if ($db->errno != 0) {
            return $db->error;
        }
        return $db->affected_rows > 0 ? true : false;
    }
?>