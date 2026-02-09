<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SekolahIndonesiaApiService;

class TestApiController extends Controller
{
    protected $apiService;

    public function __construct()
    {
        // Constructor kosong karena kita akan mengakses service melalui app() helper
    }

    public function testApi()
    {
        try {
            // Akses service melalui app() helper
            $apiService = app(SekolahIndonesiaApiService::class);

            // Coba ambil beberapa sekolah sebagai tes
            $results = $apiService->getSekolahByName('SMK', 5);
            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'API Sekolah Indonesia berfungsi dengan baik'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengakses API: ' . $e->getMessage()
            ], 500);
        }
    }
}
