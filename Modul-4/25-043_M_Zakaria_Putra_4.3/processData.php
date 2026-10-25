<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Process Data</title>
</head>
<body>
// Praktikum Modul 4 : Form Input Handling 
// File ini menerima data dari processData_form.html 
// Data dikirim dengan method POST 
// Isi $_POST ditampilkan memakai foreach 

<div>
<h3>Posted data: </h3>
<?php
foreach ($_GET as $key => $value)
    echo "($key) => ($value)<br/>";

// $surname = $_POST['surname'];
?>
</div>
</body>
</html>