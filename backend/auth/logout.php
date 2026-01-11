<?php

require_once __DIR__ . '/../shared/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  errorResponse("Érvénytelen kérés");
}

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  errorResponse("Nincs aktív bejelentkezés");
}

session_unset();
session_destroy();

successResponse("Sikeres kijelentkezés", null);
