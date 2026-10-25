<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Process Data 2</title>
</head>
<body>

<div>
<?php
function validateName(&$errors, $field_list, $field_name)
{
    $pattern = "/^[a-zA-Z'-]+$/"; // format surname (alfabet)
    if (!isset($field_list[$field_name]) || empty($field_list[$field_name]))
        $errors[$field_name] = 'required';
    else if (!preg_match($pattern, $field_list[$field_name]))
        $errors[$field_name] = 'invalid';
}

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
// Ditambahkan kembali agar data-data form tetap terlihat di bawahnya
foreach ($_POST as $key => $value) {
    echo "<strong>$key</strong> => $value <br/>";
}
?>
</div>

</body>
</html>