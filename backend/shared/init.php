<?php

// Alap beállítások
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// CORS kezelés
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://127.0.0.1:5500') {
  header("Access-Control-Allow-Origin: $origin");
  header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Preflight válasz
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// Konfiguráció és alap modulok
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/security.php';

// Közös segédfüggvények
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/lego_helpers.php';
