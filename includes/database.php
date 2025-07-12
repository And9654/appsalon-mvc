<?php

$db = mysqli_connect(
    $_ENV['DB_HOST'], 
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);

// IMPORTANTE: usar utf8mb4 sin guion
$db->set_charset('utf8mb4');

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.<br>";
    echo "errno de depuración: " . mysqli_connect_errno() . "<br>";
    echo "error de depuración: " . mysqli_connect_error() . "<br>";
    exit;
}