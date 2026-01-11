<?php

require_once __DIR__ . '/../shared/init.php';

$token = $_GET['token'] ?? '';

if ($token === '') {
  http_response_code(400);
  errorResponse("Hiányzó token");
}

try {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE verify_token = ? AND is_active = 0");
  $stmt->execute([$token]);
  $user = $stmt->fetch();

  if (!$user) {
    http_response_code(400);
    errorResponse("Érvénytelen vagy már aktivált token");
  }

  $stmt = $pdo->prepare("UPDATE users SET is_active = 1, verify_token = NULL WHERE id = ?");
  $stmt->execute([$user['id']]);

  successResponse("Fiók sikeresen aktiválva", null);
} catch (PDOException $e) {
  http_response_code(500);
  errorResponse("Adatbázis hiba");
}
