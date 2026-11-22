<?php
// dipasang di halaman Data Master, hanya level 1 (owner) yang boleh masuk
if ($_SESSION['level'] != 1) {
    header("Location: index.php?pesan=akses");
    exit;
}
