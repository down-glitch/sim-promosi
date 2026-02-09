<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SekolahIndonesiaApiService
{
    protected $baseUrl = 'https://api-sekolah-indonesia.vercel.app/api';

    /**
     * Mendapatkan daftar sekolah berdasarkan nama
     */
    public function getSekolahByName(string $namaSekolah, int $limit = 10)
    {
        $cacheKey = "sekolah_nama_" . md5($namaSekolah . $limit);
        
        return Cache::remember($cacheKey, 3600, function () use ($namaSekolah, $limit) {
            try {
                $response = Http::get($this->baseUrl . '/sekolah/nama/' . urlencode($namaSekolah) . '?limit=' . $limit);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                \Log::error('Error fetching schools by name: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Mendapatkan daftar sekolah berdasarkan provinsi
     */
    public function getSekolahByProvince(string $provinsi, int $limit = 100)
    {
        $cacheKey = "sekolah_provinsi_" . md5($provinsi . $limit);
        
        return Cache::remember($cacheKey, 3600, function () use ($provinsi, $limit) {
            try {
                $response = Http::get($this->baseUrl . '/sekolah/provinsi/' . urlencode($provinsi) . '?limit=' . $limit);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                \Log::error('Error fetching schools by province: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Mendapatkan daftar sekolah berdasarkan kabupaten/kota
     */
    public function getSekolahByCity(string $kabupaten, int $limit = 100)
    {
        $cacheKey = "sekolah_kabupaten_" . md5($kabupaten . $limit);
        
        return Cache::remember($cacheKey, 3600, function () use ($kabupaten, $limit) {
            try {
                $response = Http::get($this->baseUrl . '/sekolah/kabupaten/' . urlencode($kabupaten) . '?limit=' . $limit);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                \Log::error('Error fetching schools by city: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Mendapatkan detail sekolah berdasarkan ID
     */
    public function getSekolahById(string $idSekolah)
    {
        $cacheKey = "sekolah_id_" . $idSekolah;
        
        return Cache::remember($cacheKey, 3600, function () use ($idSekolah) {
            try {
                $response = Http::get($this->baseUrl . '/sekolah/id/' . urlencode($idSekolah));
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return null;
            } catch (\Exception $e) {
                \Log::error('Error fetching school by ID: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Mencari sekolah dengan berbagai parameter
     */
    public function searchSekolah(array $params = [])
    {
        $queryString = http_build_query($params);
        $cacheKey = "sekolah_search_" . md5($queryString);
        
        return Cache::remember($cacheKey, 3600, function () use ($params) {
            try {
                $url = $this->baseUrl . '/sekolah/search?' . http_build_query($params);
                $response = Http::get($url);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                \Log::error('Error searching schools: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Mendapatkan daftar provinsi
     */
    public function getProvinsi()
    {
        $cacheKey = "daftar_provinsi";
        
        return Cache::remember($cacheKey, 86400, function () {
            try {
                $response = Http::get($this->baseUrl . '/wilayah/provinsi');
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                \Log::error('Error fetching provinces: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Mendapatkan daftar kabupaten/kota berdasarkan provinsi
     */
    public function getKabupatenByProvince(string $provinsi)
    {
        $cacheKey = "kabupaten_provinsi_" . md5($provinsi);
        
        return Cache::remember($cacheKey, 86400, function () use ($provinsi) {
            try {
                $response = Http::get($this->baseUrl . '/wilayah/kabupaten/' . urlencode($provinsi));
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                \Log::error('Error fetching cities by province: ' . $e->getMessage());
                return [];
            }
        });
    }
}