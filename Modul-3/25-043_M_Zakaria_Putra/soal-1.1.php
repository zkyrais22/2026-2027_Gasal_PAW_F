<?php
$fruits = array("Avocado", "Blueberry", "Cherry");

echo "<h3>1.1</h3>";
array_push($fruits, "Durian", "Elderberry", "Fig", "Grape", "Honeydew");
echo 'fruits = ( "' . implode('", "', $fruits) . '" )<br>';
echo "Nilai dengan indeks tertinggi: " . end($fruits) . "<br>";

echo "<h3>1.2</h3>";
unset($fruits[1]);
echo "Data Blueberry dihapus.<br>";
echo 'fruits = ( "' . implode('", "', $fruits) . '" )<br>';
echo "Nilai dengan indeks tertinggi: " . end($fruits);
?>