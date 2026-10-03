<?php 
$matkul = ["PTI", "ALPRO", "DPW", "STRUKDAT", "JARKOM", "PAW", "PSBF", "RPL"];
$praktikum = ["JARKOM", "PAW"];

for($i = 0; $i < count($matkul); $i++) {
	
	// Cek elemen $matkul 
	if ($matkul[$i] == $praktikum[0] || $matkul[$i] == $praktikum[1]) {
		echo "Saya sedang mengambil matkul " .  $matkul[$i] . " termasuk praktikum nya<br> ";
	}

	// Cek index array 
	elseif ($i == 6 || $i ==7) {
		echo "Saya belum mengambil matkul " . $matkul[$i] . "<br>";
	}

	// Terakhir
	else {
		echo "Saya sudah mengambil matkul " . $matkul[$i] . " semester lalu<br>";
	}
}
?>