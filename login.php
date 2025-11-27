<?php
require_once "functions.php";

// vytvoříme verify kód při načtení stránky
if (!isset($_SESSION["verify"])) {
    $_SESSION["verify"] = strval(rand(10000, 99999));
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];
    $verify = trim($_POST["verify"]);

    // captcha kontrola
    if ($verify !== $_SESSION["verify"]) {
        $error = "Verify kód není správně!";
    } else {

        $user = findUserByEmail($email);

        if (!$user || !password_verify($pass, $user["password_hash"])) {
            $error = "Špatný email nebo heslo!";
        } else {
            unset($_SESSION["verify"]);
            $_SESSION["uid"] = $user["id"];
            header("Location: dashboard.php");
            exit;
        }
    }

    // vytvořit nový verify kód po každém pokusu
    $_SESSION["verify"] = strval(rand(10000, 99999));
}
?>

<link rel="stylesheet" href="style/style.css">

<div class="auth-container">
    <h2>Přihlášení</h2>

    <?php if ($error) echo "<div class='error'>$error</div>"; ?>

    <form method="post">

        <label>Email:</label>
        <input name="email" type="email" required>

        <label>Heslo:</label>
        <input name="password" type="password" required>

        <label>Verify kód:</label>
        <div style="
            padding:10px; 
            background:#eee; 
            margin-bottom:10px;
            text-align:center;
            font-size:22px;
            font-weight:bold;
            letter-spacing:4px;
        ">
            <?= $_SESSION["verify"] ?>
        </div>

        <input type="text" name="verify" placeholder="Zadej verify kód" required>

        <button>Přihlásit se</button>
    </form>

    <p style="text-align:center;margin-top:10px;">
        Nemáš účet? <a href="register.php">Registrace</a>
    </p>
</div>
