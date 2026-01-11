<?php

$dsn  = "mysql:host=localhost;dbname=legora;charset=utf8mb4";
$user = "root";
$pass = "";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die(json_encode([
        "status"  => "error",
        "message" => "Adatbázis kapcsolat hiba",
        "data"    => null
    ]));
}
