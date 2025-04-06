<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$charset = getenv('DB_CHARSET');

function getDatabaseConnection()
{
    global $host, $dbname, $username, $password, $charset;

    try {
        // Create a PDO instance and return it
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $pdo = new PDO($dsn, $username, $password);

        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch (PDOException $e) {
        // Handle connection error
        echo 'Connection failed: ' . $e->getMessage();
        return null;
    }
}
