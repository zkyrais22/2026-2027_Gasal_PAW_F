<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Process Data 2</title>
</head>
<body>

<div>
<?php
require 'validate2.inc';

$errors = array();
validateName($errors, $_POST, 'surname');

if ($errors)
{
    echo '<p style="color: red; font-weight: bold;">Errors:</p>';
    foreach ($errors as $field => $error)
        echo "$field $error<br/>";
}
else
{
    echo '<p style="color: green; font-weight: bold;">Data OK!</p>';
}
?>
</div>

<hr>

<div>
<h3>Posted Data Details:</h3>
<?php
foreach ($_POST as $key => $value) {
    echo "<strong>$key</strong> => $value <br/>";
}
?>
</div>

</body>
</html>