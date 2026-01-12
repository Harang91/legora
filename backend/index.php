<?php

// Alap backend inicializálás: segédfüggvények betöltése
include "./sql_fuggvenyek.php";

// HTTP metódus beolvasása (GET, POST, stb.)
$metodus = $_SERVER["REQUEST_METHOD"];

// URI feldolgozása (útvonal szegmentálása)
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri = explode("/", $uri);

// JSON body beolvasása (POST/PUT esetén)
$bodyAdatok = json_decode(file_get_contents("php://input"), true);
