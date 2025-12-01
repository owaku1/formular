<?php
require_once "functions.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Neplatný email!";

    if (strlen($username) < 3)
        $errors[] = "Nick je moc krátký!";

    if (strlen($password) < 4)
        $errors[] = "Heslo musí mít alespoň 4 znaky!";

    if ($password !== $password2)
        $errors[] = "Hesla se neshodují!";

    if (empty($errors)) {
        if (findUserByEmail($email)) {
            $errors[] = "Tento email je již použit!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, username, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$email, $username, $hash]);
            $success = "Registrace úspěšná!";
        }
    }
}
?>

<link rel="stylesheet" href="style/style.css">

<div class="dark-toggle" onclick="toggleDark()">🌙 / ☀️</div>

<div class="auth-container">

    <h2 class="clean-title">Registrace</h2>
    <p class="clean-subtitle">Vytvoř si svůj účet.</p>

    <?php foreach ($errors as $e): ?>
        <div class="error"><?= $e ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
        <script>showToast("<?= $success ?>");</script>
    <?php endif; ?>

    <form method="post">

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Nick</label>
        <input type="text" name="username" required>

        <label>Heslo</label>
        <input type="password" name="password" required>

        <label>Heslo znovu</label>
        <input type="password" name="password2" required>

        <button>Registrovat</button>
    </form>

    <p class="clean-back">
        Máš účet? <a href="login.php">Přihlásit</a>
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
