<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SekolahIndonesiaApiService;
use Illuminate\Support\Facades\DB;

class SchoolVerificationController extends Controller
{
    protected $apiService;

    public function __construct()
    {
        $this->apiService = app(SekolahIndonesiaApiService::class);
    }

    /**
     * Autocomplete nama sekolah
     */
    public function autocompleteSekolah(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);

        if (empty($query)) {
            return response()->json([]);
        }

        // Cari dari tabel mstr_schools di database lokal
        $schools = DB::table('mstr_schools')
            ->where(function($q) use ($query) {
                $q->where('NAME', 'LIKE', "%{$query}%")
                  ->orWhere('CITY', 'LIKE', "%{$query}%")
                  ->orWhere('PROVINCE', 'LIKE', "%{$query}%");
            })
            ->select('INSTITUTION_CODE as id', 'NAME as text', 'ADDRESS as alamat', 'PROVINCE as provinsi', 'CITY as kabupaten')
            ->limit($limit)
            ->get()
            ->toArray();

        // Format hasil untuk Select2
        $formattedResults = array_map(function($school) {
            return [
                'id' => $school->id,
                'text' => $school->text,
                'alamat' => $school->alamat,
                'provinsi' => $school->provinsi,
                'kabupaten' => $school->kabupaten,
            ];
        }, $schools);

        return response()->json($formattedResults);
    }

    /**
     * Autocomplete provinsi
     */
    public function getProvinsi(Request $request)
    {
        $query = $request->input('q', '');

        $provinsi = DB::table('mstr_schools')
            ->when(!empty($query), function($q) use ($query) {
                return $q->where('PROVINCE', 'LIKE', "%{$query}%");
            })
            ->select('PROVINCE as id', 'PROVINCE as text')
            ->distinct()
            ->orderBy('PROVINCE')
            ->limit(20)
            ->get()
            ->toArray();

        // Format hasil untuk Select2
        $formattedResults = array_map(function($prov) {
            return [
                'id' => $prov->id,
                'text' => $prov->text,
            ];
        }, $provinsi);

        return response()->json($formattedResults);
    }

    /**
     * Autocomplete kabupaten berdasarkan provinsi
     */
    public function getKabupatenByProvince(Request $request, $provinsi)
    {
        $query = $request->input('q', '');

        $kabupaten = DB::table('mstr_schools')
            ->where('PROVINCE', $provinsi)
            ->when(!empty($query), function($q) use ($query) {
                return $q->where('CITY', 'LIKE', "%{$query}%");
            })
            ->select('CITY as id', 'CITY as text')
            ->distinct()
            ->orderBy('CITY')
            ->limit(20)
            ->get()
            ->toArray();

        // Format hasil untuk Select2
        $formattedResults = array_map(function($kab) {
            return [
                'id' => $kab->id,
                'text' => $kab->text,
            ];
        }, $kabupaten);

        return response()->json($formattedResults);
    }

    /**
     * Verifikasi sekolah berdasarkan nama
     */
    public function verifySekolah(Request $request)
    {
        $namaSekolah = $request->input('nama_sekolah');

        if (empty($namaSekolah)) {
            return response()->json([
                'valid' => false,
                'message' => 'Nama sekolah tidak boleh kosong'
            ]);
        }

        $results = $this->apiService->getSekolahByName($namaSekolah, 1);

        if (!empty($results) && count($results) > 0) {
            $school = $results[0];
            return response()->json([
                'valid' => true,
                'message' => 'Sekolah ditemukan',
                'data' => [
                    'id' => $school['id'] ?? $school['npsn'] ?? $school['nama_sekolah'],
                    'nama_sekolah' => $school['nama_sekolah'] ?? $school['sekolah'],
                    'alamat' => $school['alamat'] ?? $school['alamat_lengkap'] ?? '',
                    'provinsi' => $school['provinsi'] ?? '',
                    'kabupaten' => $school['kabupaten_kota'] ?? $school['kabupaten'] ?? '',
                ]
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Sekolah tidak ditemukan dalam database nasional'
        ]);
    }

    /**
     * Dapatkan daftar provinsi
     */
    public function getProvinsi()
    {
        $results = $this->apiService->getProvinsi();

        $formattedResults = array_map(function($provinsi) {
            return [
                'id' => $provinsi['id'] ?? $provinsi['kode_provinsi'] ?? $provinsi['nama'],
                'text' => $provinsi['nama'] ?? $provinsi['nama_provinsi'] ?? $provinsi,
            ];
        }, $results);

        return response()->json($formattedResults);
    }

    /**
     * Dapatkan daftar kabupaten berdasarkan provinsi
     */
    public function getKabupatenByProvince($provinsi)
    {
        $results = $this->apiService->getKabupatenByProvince(urldecode($provinsi));

        $formattedResults = array_map(function($kabupaten) {
            return [
                'id' => $kabupaten['id'] ?? $kabupaten['kode_kabupaten'] ?? $kabupaten['nama'],
                'text' => $kabupaten['nama'] ?? $kabupaten['nama_kabupaten'] ?? $kabupaten,
            ];
        }, $results);

        return response()->json($formattedResults);
    }

    /**
     * Cari sekolah berdasarkan berbagai kriteria
     */
    public function searchSekolah(Request $request)
    {
        $params = $request->all();
        $results = $this->apiService->searchSekolah($params);

        return response()->json($results);
    }
}
