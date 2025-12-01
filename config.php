<?php
session_start();

// všechny možné kombinace pro MAMP a XAMPP
$logins = [
    ["root", ""],       // XAMPP
    ["root", "root"],   // MAMP
];

// budeme testovat
$connected = false;
$errorMsg = "";

foreach ($logins as $login) {
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=formular;charset=utf8mb4",
            $login[0],
            $login[1]
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // success!
        $connected = true;
        break;

    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
    }
}

if (!$connected) {
    die("DB ERROR: " . $errorMsg);
}
