<?php
require_once "functions.php";
requireLogin();

$user = findUserById($_SESSION["uid"]);
$errors = [];
$success = "";

// Pokud složka uploads neexistuje → vytvořit automaticky
if (!is_dir("uploads")) {
    mkdir("uploads", 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // email + nick
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Neplatný email";
    if (strlen($username) < 3) $errors[] = "Nick je moc krátký";

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

    // AVATAR UPLOAD
    $avatarPath = $user["avatar"];

    if (!empty($_FILES["avatar"]["name"])) {

        $file = $_FILES["avatar"];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
            $errors[] = "Avatar musí být JPG/PNG/GIF";
        } else {
            $newName = "uploads/" . uniqid("av_") . "." . $ext;

            if (move_uploaded_file($file["tmp_name"], $newName)) {
                $avatarPath = $newName;
            } else {
                $errors[] = "Nepodařilo se nahrát avatar.";
            }
        }
    }

    // UPDATE PROFILU
    if (empty($errors)) {
        $q = $pdo->prepare("UPDATE users SET email=?, username=?, avatar=? WHERE id=?");
        $q->execute([$email, $username, $avatarPath, $user["id"]]);

        $success = "Profil byl úspěšně aktualizován!";
        $user = findUserById($user["id"]);
    }
}
?>

<link rel="stylesheet" href="style/style.css">

<div class="auth-container">
    <h2>Nastavení profilu</h2>

    <?php foreach ($errors as $e) echo "<div class='error'>$e</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <form method="post" enctype="multipart/form-data">

        <label>Email:</label>
        <input name="email" value="<?= htmlspecialchars($user["email"]) ?>">

        <label>Nick:</label>
        <input name="username" value="<?= htmlspecialchars($user["username"]) ?>">

        <label>Avatar:</label>
        <input type="file" name="avatar">

        <?php if ($user["avatar"]): ?>
            <img src="<?= $user["avatar"] ?>" class="avatar-img">
        <?php endif; ?>

        <hr>

        <label>Nové heslo (volitelné):</label>
        <input type="password" name="new_password">

        <label>Nové heslo znovu:</label>
        <input type="password" name="new_password2">

        <button>Uložit změny</button>
    </form>

    <p style="text-align:center;margin-top:15px;">
        <a href="dashboard.php">Zpět</a>
    </p>
</div>

