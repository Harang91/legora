<?php

// Egységes JSON válaszok

function successResponse(string $message, $data = null): void
{
  http_response_code(200);
  echo json_encode([
    "status" => "success",
    "message" => $message,
    "data" => $data
  ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  exit;
}

function errorResponse(string $message, $data = null, int $code = 400): void
{
  http_response_code($code);
  echo json_encode([
    "status" => "error",
    "message" => $message,
    "data" => $data
  ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  exit;
}
