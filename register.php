<?php
require_once "functions.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $pass = $_POST["password"];
    $pass2 = $_POST["password2"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Neplatný email";
    if (strlen($username) < 3) $errors[] = "Nick je moc krátký";
    if ($pass !== $pass2) $errors[] = "Hesla se neshodují";
    if (strlen($pass) < 6) $errors[] = "Heslo musí mít aspoň 6 znaků";
    if (findUserByEmail($email)) $errors[] = "Email už existuje";

    if (empty($errors)) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        global $pdo;

        $q = $pdo->prepare("INSERT INTO users (email, username, password_hash) VALUES (?, ?, ?)");
        $q->execute([$email, $username, $hash]);

        $success = "Registrace proběhla úspěšně!";
    }
}
?>

<link rel="stylesheet" href="style/style.css">

<div class="auth-container">
    <h2>Registrace</h2>

    <?php foreach ($errors as $e) echo "<div class='error'>$e</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <form method="post">
        <label>Email:</label>
        <input name="email" type="email" required>

        <label>Nick:</label>
        <input name="username" type="text" required>

        <label>Heslo:</label>
        <input name="password" type="password" required>

        <label>Heslo znovu:</label>
        <input name="password2" type="password" required>

        <button>Registrovat</button>
    </form>

    <p style="text-align:center;margin-top:10px;">
        Máš účet? <a href="login.php">Přihlásit se</a>
    </p>
</div>
