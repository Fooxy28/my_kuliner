<?php

declare(strict_types=1);

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'my_kuliner';
$dbPort = 3306;

$conn = mysqli_init();
mysqli_real_connect($conn, $dbHost, $dbUser, $dbPass, $dbName, $dbPort);

if (mysqli_connect_errno()) {
    http_response_code(500);
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
