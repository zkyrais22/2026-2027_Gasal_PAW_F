<?php
// array_push
$a = array("A");
echo 'Array awal: ("A")<br>';
array_push($a, "B");
echo "Hasil array_push: " . implode(" ", $a) . "<br><br>";

// array_merge
$a1 = array("A", "B");
$a2 = array("C");
echo 'Array awal: ("A", "B") digabung dengan ("C")<br>';
echo "Hasil array_merge: " . implode(" ", array_merge($a1, $a2)) . "<br><br>";

// array_values
$assoc = array("x" => 1, "y" => 2);
echo 'Array awal: ("x" => 1, "y" => 2)<br>';
echo "Hasil array_values: " . implode(" ", array_values($assoc)) . "<br><br>";

// array_search
$cari = array("A", "B", "C");
echo 'Mencari "B" pada array: ("A", "B", "C")<br>';
echo "Hasil array_search: " . array_search("B", $cari) . "<br><br>";

// array_filter
$campur = array(0, 1, false, 2, "", 3, "array");
echo 'Array awal: (0, 1, false, 2, "", 3, "array")<br>';
echo "Hasil array_filter: " . implode(" ", array_filter($campur)) . "<br><br>";

// sort & rsort
$angka = array(3, 1, 2);
echo "Array awal: (3, 1, 2)<br>";
$s = $angka;
sort($s);
echo "Hasil sort: " . implode(" ", $s) . "<br>";
$r = $angka;
rsort($r);
echo "Hasil rsort: " . implode(" ", $r) . "<br><br>";

// sorting array asosiatif
function tampilAssoc($arr) {
    foreach ($arr as $k => $v) {
        echo $k . "=> " . $v . ", ";
    }
    echo "<br>";
}

$umur = array("Peter" => 35, "Ben" => 37, "Joe" => 43);
echo 'Array awal: ("Peter"=>35, "Ben"=>37, "Joe"=>43)<br>';

$u = $umur; asort($u);
echo "Hasil asort: "; tampilAssoc($u);

$u = $umur; ksort($u);
echo "Hasil ksort: "; tampilAssoc($u);

$u = $umur; arsort($u);
echo "Hasil arsort: "; tampilAssoc($u);

$u = $umur; krsort($u);
echo "Hasil krsort: "; tampilAssoc($u);
?>