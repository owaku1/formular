<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "functions.php";
requireLogin();

$user = findUserById($_SESSION["uid"]);
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Neplatný email";

    if (strlen($username) < 3)
        $errors[] = "Nick je moc krátký";

    // změna hesla (volitelné)
    if (!empty($_POST["new_password"])) {
        if ($_POST["new_password"] !== $_POST["new_password2"]) {
            $errors[] = "Hesla se neshodují";
        } else {
            $hash = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?")
                ->execute([$hash, $user["id"]]);
        }
    }

    // AVATAR (BASE64)
    $avatar = $user["avatar"];

    if (!empty($_FILES["avatar"]["name"])) {
        $tmp = $_FILES["avatar"]["tmp_name"];
        $ext = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, ["jpg","jpeg","png","gif"])) {
            $errors[] = "Avatar musí být JPG/PNG/GIF";
        } else {
            $mime = ($ext == "png") ? "image/png" :
                    (($ext == "gif") ? "image/gif" : "image/jpeg");

            $data = file_get_contents($tmp);
            $avatar = "data:$mime;base64," . base64_encode($data);
        }
    }

    if (empty($errors)) {
        $pdo->prepare("UPDATE users SET email=?, username=?, avatar=? WHERE id=?")
            ->execute([$email, $username, $avatar, $user["id"]]);
        $success = "Změny uloženy!";
        $user = findUserById($user["id"]);
    }
}
?>

<link rel="stylesheet" href="style/style.css">

<div class="dark-toggle" onclick="toggleDark()">🌙 / ☀️</div>

<div class="auth-container clean-profile">

    <h2 class="clean-title">Nastavení profilu</h2>
    <p class="clean-subtitle">Uprav avatar, email, nick nebo heslo.</p>

    <?php foreach ($errors as $e): ?>
        <div class="error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
        <script>showToast("<?= htmlspecialchars($success) ?>")</script>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="clean-form">

        <!-- skrytý input -->
        <input type="file" id="avatarInput" name="avatar" style="display:none;">

        <!-- klikací avatar -->
        <div class="clean-avatar-wrapper">
            <div class="avatar-click">
                <img id="avatarPreview" class="avatar-img"
                     src="<?= $user['avatar'] ?: 'https://via.placeholder.com/200' ?>">
            </div>
            <p class="clean-avatar-text">Klikni pro změnu avataru</p>
        </div>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" required>

        <label>Nick</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user["username"]) ?>" required>

        <label>Nové heslo</label>
        <input type="password" name="new_password">

        <label>Nové heslo znovu</label>
        <input type="password" name="new_password2">

        <button class="clean-save">Uložit změny</button>

        <p class="clean-back">
            <a href="dashboard.php">← Zpět</a>
        </p>

    </form>
</div>

<div id="toast" class="toast"></div>

<script>
function toggleDark(){
    document.body.classList.toggle("dark");
    localStorage.setItem("darkmode", document.body.classList.contains("dark"));
}
if(localStorage.getItem("darkmode")==="true") document.body.classList.add("dark");

document.querySelector(".avatar-click").onclick = () => {
    document.getElementById("avatarInput").click();
};

document.getElementById("avatarInput").onchange = e => {
    let file = e.target.files[0];
    if (!file) return;
    let r = new FileReader();
    r.onload = ev => document.getElementById("avatarPreview").src = ev.target.result;
    r.readAsDataURL(file);
};

function showToast(msg){
    const t=document.getElementById("toast");
    t.innerText=msg;
    t.classList.add("show");
    setTimeout(()=>t.classList.remove("show"),2500);
}
</script>
