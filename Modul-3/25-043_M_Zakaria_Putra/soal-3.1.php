<?php
function tampilHeight($arr) {
    $hasil = array();
    foreach ($arr as $key => $value) {
        $hasil[] = '"' . $key . '"=>"' . $value . '"';
    }
    return 'height = (' . implode(", ", $hasil) . ')';
}

$height = array("Andy"=>"176", "Barry"=>"165", "Charlie"=>"170");

$height["David"]  = "180";
$height["Ethan"]  = "172";
$height["Frank"]  = "168";
$height["George"] = "175";
$height["Harry"]  = "182";

echo tampilHeight($height) . "<br>";
echo "Nilai dengan indeks terakhir: " . end($height) . "<br><br>";

unset($height["Barry"]); // hapus data Barry

echo tampilHeight($height) . "<br>";
echo "Nilai dengan indeks terakhir setelah dihapus: " . end($height);
?>