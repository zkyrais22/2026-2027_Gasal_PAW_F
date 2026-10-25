<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Form Input Handling</title>
<style type="text/css">
.field { margin: 4px 0; }
.field label { display: inline-block; width: 110px; text-align: right; margin-right: 6px; }
fieldset { background-color: #ccccff; width: 420px; }
</style>
</head>
<body>
<h1>Register</h1>
<?php
$errors = array();
if (isset($_POST['surname']))
{
    require 'validate.inc';
    validateName($errors, $_POST, 'surname');
    validateEmail($errors, $_POST, 'email');
    validatePassword($errors, $_POST, 'passwd');
    validateAddress($errors, $_POST, 'address');
    validateState($errors, $_POST, 'state');
    validateGender($errors, $_POST, 'sex');

    if ($errors)
    {
        echo '<h1>Invalid, correct the following errors:</h1>';
        foreach ($errors as $field => $error)
            echo "$field $error<br/>";

        // tampilkan kembali form beserta isian sebelumnya
        include 'form2.inc';
    }
    else
    {
        echo 'Form submitted successfully with no errors';
    }
}
else
{
    // tampilkan form kosong
    include 'form2.inc';
}
?>
</body>
</html>