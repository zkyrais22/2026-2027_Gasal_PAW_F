<?php
$weight = array("Andy"=>"70", "Barry"=>"65", "Charlie"=>"75");

$hasil = array();
foreach ($weight as $key => $value) {
    $hasil[] = '"' . $key . '"=>"' . $value . '"';
}
echo 'weight = (' . implode(", ", $hasil) . ')<br>';

$nilai = array_values($weight);
echo "Data kedua: " . $nilai[1];
?>