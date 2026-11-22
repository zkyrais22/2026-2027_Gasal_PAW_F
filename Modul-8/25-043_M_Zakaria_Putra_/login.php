<?php
session_start();

// kalau sudah login, tidak boleh membuka halaman login lagi
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include 'koneksi.php';

$error    = "";
$username = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username == "" || $password == "") {
        $error = "Username dan password harus diisi.";
    } else {
        // password di database disimpan dengan MD5
        $pass_md5 = md5($password);

        $stmt = mysqli_prepare($koneksi,
            "SELECT id_user, nama, level FROM user WHERE username = ? AND password = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $pass_md5);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user) {
            // login berhasil, simpan data ke session
            session_regenerate_id(true);
            $_SESSION['login']   = true;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['level']   = $user['level'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - Sistem Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; background: whitesmoke; }
        .kotak { width: 320px; margin: 90px auto; background: white; border: 1px solid lightgray; }
        .judul { background: #0d4a8f; color: white; text-align: center; padding: 12px; font-weight: bold; }
        .isi { padding: 20px; }
        label { display: block; margin-bottom: 4px; }
        input { width: 100%; padding: 7px; margin-bottom: 14px; box-sizing: border-box; }
        button { width: 100%; padding: 9px; background: #0d4a8f; color: white; border: 0; cursor: pointer; }
        .gagal { background: mistyrose; padding: 10px; border: 1px solid red; margin-bottom: 14px; }
    </style>
</head>
<body>
    <div class="kotak">
        <div class="judul">Sistem Penjualan</div>
        <div class="isi">
            <?php if ($error != "") { ?>
                <div class="gagal"><?= e($error) ?></div>
            <?php } ?>
            <form method="post">
                <label>Username</label>
                <input type="text" name="username" value="<?= e($username) ?>" autofocus>
                <label>Password</label>
                <input type="password" name="password">
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
