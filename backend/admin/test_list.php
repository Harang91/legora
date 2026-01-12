<?php

// Egyszerű szerver-elérhetőségi teszt (health check endpoint)
header('Content-Type: application/json');

echo json_encode([
    "status" => "success",
    "message" => "A szerver elérhető!"
]);
