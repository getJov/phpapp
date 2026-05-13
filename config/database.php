<?php

declare(strict_types=1);

// Local defaults for this simple app. Edit these values for your MySQL setup.
$dbHost = 'localhost';
$dbName = 'phpapp_crud';
$dbUser = 'root';
$dbPass = 'Evezaiyeh012723!';
$dbCharset = 'utf8mb4';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $pdoOptions);
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Database connection failed. Check config/database.php.');
}
