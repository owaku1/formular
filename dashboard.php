<?php
require_once "functions.php";
requireLogin();

$user = findUserById($_SESSION["uid"]);
?>

<link rel="stylesheet" href="style/style.css">

<div class="dark-toggle" onclick="toggleDark()">🌙 / ☀️</div>

<div class="auth-container" style="text-align:center;">
    
    <h2 class="clean-title">Vítej, <?= htmlspecialchars($user["username"]) ?>!</h2>
    <p class="clean-subtitle">Tady najdeš vše podstatné.</p>

    <?php if (!empty($user["avatar"])): ?>
        <img src="<?= $user['avatar'] ?>" class="avatar-img" style="width:150px;height:150px;margin-bottom:20px;">
    <?php else: ?>
        <img src="https://via.placeholder.com/150" class="avatar-img" style="margin-bottom:20px;">
    <?php endif; ?>

    <a href="profile.php"><button>⚙️ Nastavení profilu</button></a>
    <a href="logout.php"><button style="margin-top:10px;">🚪 Odhlásit</button></a>

</div>

<script>
function toggleDark(){
    document.body.classList.toggle("dark");
    localStorage.setItem("darkmode", document.body.classList.contains("dark"));
}
if(localStorage.getItem("darkmode")==="true") document.body.classList.add("dark");
</script>
