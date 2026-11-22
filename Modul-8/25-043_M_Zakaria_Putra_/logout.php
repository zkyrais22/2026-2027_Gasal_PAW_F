<?php
session_start();

// hapus semua session yang dibuat saat login
unset($_SESSION['login']);
unset($_SESSION['id_user']);
unset($_SESSION['nama']);
unset($_SESSION['level']);
session_destroy();

header("Location: login.php");
exit;
