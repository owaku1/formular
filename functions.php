<?php
require_once "config.php";

function findUserByEmail($email) {
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $q->execute([$email]);
    return $q->fetch();
}

function findUserById($id) {
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $q->execute([$id]);
    return $q->fetch();
}

function isLogged() {
    return isset($_SESSION["uid"]);
}

function requireLogin() {
    if (!isLogged()) {
        header("Location: login.php");
        exit;
    }
}
