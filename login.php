<?php
require_once "functions.php";

if (!isset($_SESSION["verify_code"])) {
    $_SESSION["verify_code"] = rand(10000, 99999);
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $verify = trim($_POST["verify"]);

    if ($verify !== strval($_SESSION["verify_code"])) {
        $errors[] = "Špatný ověřovací kód!";
    } else {
        $user = findUserByEmail($email);

        if (!$user || !password_verify($password, $user["password_hash"])) {
            $errors[] = "Špatný email nebo heslo!";
        } else {
            unset($_SESSION["verify_code"]);
            $_SESSION["uid"] = $user["id"];
            header("Location: dashboard.php");
            exit;
        }
    }

    $_SESSION["verify_code"] = rand(10000, 99999);
}
?>

<link rel="stylesheet" href="style/style.css">

<div class="dark-toggle" onclick="toggleDark()">🌙 / ☀️</div>

<div class="auth-container">
    <h2 class="clean-title">Přihlášení</h2>
    <p class="clean-subtitle">Zadej své údaje pro přístup.</p>

    <?php foreach ($errors as $e): ?>
        <div class="error"><?= $e ?></div>
    <?php endforeach; ?>

    <form method="post">

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Heslo</label>
        <input type="password" name="password" required>

        <label>Ověřovací kód</label>
        <div class="verify-box">
            <div class="verify-code"><?= $_SESSION["verify_code"] ?></div>
        </div>
        <input type="text" name="verify" required placeholder="Zadej kód">

        <button>Přihlásit</button>
    </form>

    <p class="clean-back">
        Nemáš účet? <a href="register.php">Registrace</a>
    </p>
</div>

<div id="toast" class="toast"></div>

<script>
function toggleDark(){
    document.body.classList.toggle("dark");
    localStorage.setItem("darkmode", document.body.classList.contains("dark"));
}
if(localStorage.getItem("darkmode")==="true") document.body.classList.add("dark");

function showToast(msg){
    const t=document.getElementById("toast");
    t.innerText=msg;
    t.classList.add("show");
    setTimeout(()=>t.classList.remove("show"),2500);
}
</script>
