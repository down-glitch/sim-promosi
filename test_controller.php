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

// Tampilkan beberapa data contoh
foreach (array_slice($roadshows, 0, 3) as $roadshow) {
    echo "- {$roadshow['provinsi']}, {$roadshow['kabupaten']}: {$roadshow['jumlah_kegiatan']} kegiatan\n";
}