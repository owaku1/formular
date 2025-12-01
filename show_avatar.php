<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "functions.php";

$id = $_GET["id"] ?? 0;
$user = findUserById($id);

if (!$user || empty($user["avatar"])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

// detekce typu podle obsahu – jednoduchá verze:
$raw = $user["avatar"];

if (substr($raw, 0, 2) === "\xFF\xD8") {
    header("Content-Type: image/jpeg");
} elseif (substr($raw, 0, 4) === "\x89PNG") {
    header("Content-Type: image/png");
} elseif (substr($raw, 0, 4) === "GIF8") {
    header("Content-Type: image/gif");
} else {
    header("Content-Type: application/octet-stream");
}

echo $raw;
exit;
