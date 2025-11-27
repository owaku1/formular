<?php
require_once "functions.php";
requireLogin();

$user = findUserById($_SESSION["uid"]);
?>
<link rel="stylesheet" href="style/style.css">

<div class="auth-container">
    <h2>Vítej, <?= htmlspecialchars($user["username"]) ?>!</h2>

    <?php if (!empty($user["avatar"])): ?>
        <img src="<?= $user["avatar"] ?>" class="avatar-img">
    <?php else: ?>
        <img src="https://via.placeholder.com/120" class="avatar-img">
    <?php endif; ?>

    <a href="profile.php">Nastavení profilu</a><br><br>
    <a href="logout.php">Odhlásit se</a>
</div>
