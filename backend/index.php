<?php
include "./sql_fuggvenyek.php";
    $metodus = $_SERVER["REQUEST_METHOD"];
    $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri = explode("/", $uri);
    $bodyAdatok = json_decode(file_get_contents("php://input"), true);

?>