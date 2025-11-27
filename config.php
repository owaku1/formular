<?php
$DB_HOST = "localhost";
$DB_NAME = "formular";
$DB_USER = "root";
$DB_PASS = "root"; // u XAMPP dej ""

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}

session_start();
