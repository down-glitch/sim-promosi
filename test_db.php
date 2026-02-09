<?php
$pdo = new PDO('sqlite:C:/Users/user/simpromosi/database/database.sqlite');

// Cek jumlah data di mstr_schools
$stmt = $pdo->query('SELECT COUNT(*) FROM mstr_schools');
$count_schools = $stmt->fetchColumn();
echo 'Jumlah sekolah: ' . $count_schools . "\n";

// Cek jumlah data di trans_input_data
$stmt = $pdo->query('SELECT COUNT(*) FROM trans_input_data');
$count_input_data = $stmt->fetchColumn();
echo 'Jumlah input data: ' . $count_input_data . "\n";

// Cek data yang ada
$stmt = $pdo->query('SELECT * FROM trans_input_data LIMIT 5');
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo 'Contoh data trans_input_data:' . "\n";
var_export($results);
?>