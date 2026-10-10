<?php 
$fruits = array("Avocado", "Blueberry", "Cherry") ;

for ($i = 1; $i <= 5; $i++) {
	$fruits[] = "Buah Tambahan " . $i;
}

$arrlength = count($fruits);

echo "panjang array saat ini: " . $arrlength . "<br><br>";

for ($x = 0; $x < $arrlength; $x++) {
	echo $fruits[$x];
	echo "<br>";
}
// perualangan for tidak perlu di ubah karena batasnya
// pakai $arrlength dari count(), yang dihitung SETELAH data ditambah
 ?>