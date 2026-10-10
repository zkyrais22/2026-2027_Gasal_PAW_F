<?php
$weight = array("Andy"=>"70", "Barry"=>"65", "Charlie"=>"75");

$hasil = array();
foreach ($weight as $k => $v) {
    $hasil[] = '"' . $k . '"=>"' . $v . '"';
}
echo 'weight = (' . implode(", ", $hasil) . ')<br><br>';

$keys = array_keys($weight);
$n = count($weight);

for ($x = 0; $x < $n; $x++) {
    echo $keys[$x] . " is " . $weight[$keys[$x]] . " kg.<br>";
}
?>