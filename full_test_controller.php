<?php

use App\Http\Controllers\RoadshowController;
use Illuminate\Http\Request;

// Buat request kosong
$request = new Request();

// Instansiasi controller
$controller = new RoadshowController();

// Panggil metode index
$result = $controller->index($request);

// Tampilkan jumlah roadshow
$roadshows = $result->getData()['roadshows'] ?? [];
echo "Jumlah wilayah roadshow: " . count($roadshows) . "\n";

// Tampilkan semua data
echo "Daftar wilayah roadshow:\n";
foreach ($roadshows as $roadshow) {
    echo "- {$roadshow['provinsi']}, {$roadshow['kabupaten']}: {$roadshow['jumlah_kegiatan']} kegiatan\n";
}

// Juga hitung total kegiatan
$totalActivities = array_sum(array_column($roadshows, 'jumlah_kegiatan'));
echo "\nTotal kegiatan: $totalActivities\n";