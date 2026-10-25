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

    if ($errors)
    {
        echo '<h1>Invalid, correct the following errors:</h1>';
        foreach ($errors as $field => $error)
            echo "$field $error<br/>";

        // tampilkan kembali form
        include 'form.inc';
    }
    else
    {
        echo 'Form submitted successfully with no errors';
    }
}
else
{
    // tampilkan form kosong
    include 'form.inc';
}
?>
</body>
</html>