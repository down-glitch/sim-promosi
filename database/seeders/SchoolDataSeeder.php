<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data sekolah untuk fitur autocomplete SIM-PROMOSI
        $schools = [
            // Data sekolah di Sumatera
            ['INSTITUTION_CODE' => 'SMKN1MEDAN', 'NAME' => 'SMK Negeri 1 Medan', 'ADDRESS' => 'Jl. SMK No. 1, Medan', 'CITY' => 'Medan', 'PROVINCE' => 'Sumatera Utara'],
            ['INSTITUTION_CODE' => 'SMAN1PALEMBANG', 'NAME' => 'SMA Negeri 1 Palembang', 'ADDRESS' => 'Jl. SMA No. 1, Palembang', 'CITY' => 'Palembang', 'PROVINCE' => 'Sumatera Selatan'],
            ['INSTITUTION_CODE' => 'SMKN1PADANG', 'NAME' => 'SMK Negeri 1 Padang', 'ADDRESS' => 'Jl. SMK No. 1, Padang', 'CITY' => 'Padang', 'PROVINCE' => 'Sumatera Barat'],
            ['INSTITUTION_CODE' => 'SMAN1PEKANBARU', 'NAME' => 'SMA Negeri 1 Pekanbaru', 'ADDRESS' => 'Jl. SMA No. 1, Pekanbaru', 'CITY' => 'Pekanbaru', 'PROVINCE' => 'Riau'],
            ['INSTITUTION_CODE' => 'SMKN1JAMBI', 'NAME' => 'SMK Negeri 1 Jambi', 'ADDRESS' => 'Jl. SMK No. 1, Jambi', 'CITY' => 'Jambi', 'PROVINCE' => 'Jambi'],
            ['INSTITUTION_CODE' => 'SMAN1BANDAACEH', 'NAME' => 'SMA Negeri 1 Banda Aceh', 'ADDRESS' => 'Jl. SMA No. 1, Banda Aceh', 'CITY' => 'Banda Aceh', 'PROVINCE' => 'Aceh'],
            ['INSTITUTION_CODE' => 'SMKN1BENGKULU', 'NAME' => 'SMK Negeri 1 Bengkulu', 'ADDRESS' => 'Jl. SMK No. 1, Bengkulu', 'CITY' => 'Bengkulu', 'PROVINCE' => 'Bengkulu'],
            ['INSTITUTION_CODE' => 'SMAN1BANDARLAMPUNG', 'NAME' => 'SMA Negeri 1 Bandar Lampung', 'ADDRESS' => 'Jl. SMA No. 1, Bandar Lampung', 'CITY' => 'Bandar Lampung', 'PROVINCE' => 'Lampung'],

            // Data sekolah di Jawa
            ['INSTITUTION_CODE' => 'SMKN1BANDUNG', 'NAME' => 'SMK Negeri 1 Bandung', 'ADDRESS' => 'Jl. SMK No. 1, Bandung', 'CITY' => 'Bandung', 'PROVINCE' => 'Jawa Barat'],
            ['INSTITUTION_CODE' => 'SMAN1SEMARANG', 'NAME' => 'SMA Negeri 1 Semarang', 'ADDRESS' => 'Jl. SMA No. 1, Semarang', 'CITY' => 'Semarang', 'PROVINCE' => 'Jawa Tengah'],
            ['INSTITUTION_CODE' => 'SMKN1YOGYAKARTA', 'NAME' => 'SMK Negeri 1 Yogyakarta', 'ADDRESS' => 'Jl. SMK No. 1, Yogyakarta', 'CITY' => 'Yogyakarta', 'PROVINCE' => 'DI Yogyakarta'],
            ['INSTITUTION_CODE' => 'SMAN1SURABAYA', 'NAME' => 'SMA Negeri 1 Surabaya', 'ADDRESS' => 'Jl. SMA No. 1, Surabaya', 'CITY' => 'Surabaya', 'PROVINCE' => 'Jawa Timur'],
            ['INSTITUTION_CODE' => 'SMKN1MALANG', 'NAME' => 'SMK Negeri 1 Malang', 'ADDRESS' => 'Jl. SMK No. 1, Malang', 'CITY' => 'Malang', 'PROVINCE' => 'Jawa Timur'],
            ['INSTITUTION_CODE' => 'SMAN1JAKARTAPUSAT', 'NAME' => 'SMA Negeri 1 Jakarta Pusat', 'ADDRESS' => 'Jl. SMA No. 1, Jakarta Pusat', 'CITY' => 'Jakarta Pusat', 'PROVINCE' => 'DKI Jakarta'],
            ['INSTITUTION_CODE' => 'SMKN1SERANG', 'NAME' => 'SMK Negeri 1 Serang', 'ADDRESS' => 'Jl. SMK No. 1, Serang', 'CITY' => 'Serang', 'PROVINCE' => 'Banten'],
            ['INSTITUTION_CODE' => 'SMAN1BOGOR', 'NAME' => 'SMA Negeri 1 Bogor', 'ADDRESS' => 'Jl. SMA No. 1, Bogor', 'CITY' => 'Bogor', 'PROVINCE' => 'Jawa Barat'],

            // Data sekolah di Bali dan Nusa Tenggara
            ['INSTITUTION_CODE' => 'SMKN1DENPASAR', 'NAME' => 'SMK Negeri 1 Denpasar', 'ADDRESS' => 'Jl. SMK No. 1, Denpasar', 'CITY' => 'Denpasar', 'PROVINCE' => 'Bali'],
            ['INSTITUTION_CODE' => 'SMAN1MATARAM', 'NAME' => 'SMA Negeri 1 Mataram', 'ADDRESS' => 'Jl. SMA No. 1, Mataram', 'CITY' => 'Mataram', 'PROVINCE' => 'Nusa Tenggara Barat'],
            ['INSTITUTION_CODE' => 'SMKN1KUPANG', 'NAME' => 'SMK Negeri 1 Kupang', 'ADDRESS' => 'Jl. SMK No. 1, Kupang', 'CITY' => 'Kupang', 'PROVINCE' => 'Nusa Tenggara Timur'],

            // Data sekolah di Kalimantan
            ['INSTITUTION_CODE' => 'SMAN1PONTIANAK', 'NAME' => 'SMA Negeri 1 Pontianak', 'ADDRESS' => 'Jl. SMA No. 1, Pontianak', 'CITY' => 'Pontianak', 'PROVINCE' => 'Kalimantan Barat'],
            ['INSTITUTION_CODE' => 'SMKN1PALANGKARAYA', 'NAME' => 'SMK Negeri 1 Palangkaraya', 'ADDRESS' => 'Jl. SMK No. 1, Palangkaraya', 'CITY' => 'Palangkaraya', 'PROVINCE' => 'Kalimantan Tengah'],
            ['INSTITUTION_CODE' => 'SMAN1BANJARMASIN', 'NAME' => 'SMA Negeri 1 Banjarmasin', 'ADDRESS' => 'Jl. SMA No. 1, Banjarmasin', 'CITY' => 'Banjarmasin', 'PROVINCE' => 'Kalimantan Selatan'],
            ['INSTITUTION_CODE' => 'SMKN1SAMARINDA', 'NAME' => 'SMK Negeri 1 Samarinda', 'ADDRESS' => 'Jl. SMK No. 1, Samarinda', 'CITY' => 'Samarinda', 'PROVINCE' => 'Kalimantan Timur'],
            ['INSTITUTION_CODE' => 'SMAN1TARAKAN', 'NAME' => 'SMA Negeri 1 Tarakan', 'ADDRESS' => 'Jl. SMA No. 1, Tarakan', 'CITY' => 'Tarakan', 'PROVINCE' => 'Kalimantan Utara'],

            // Data sekolah di Sulawesi
            ['INSTITUTION_CODE' => 'SMKN1MANADO', 'NAME' => 'SMK Negeri 1 Manado', 'ADDRESS' => 'Jl. SMK No. 1, Manado', 'CITY' => 'Manado', 'PROVINCE' => 'Sulawesi Utara'],
            ['INSTITUTION_CODE' => 'SMAN1PALU', 'NAME' => 'SMA Negeri 1 Palu', 'ADDRESS' => 'Jl. SMA No. 1, Palu', 'CITY' => 'Palu', 'PROVINCE' => 'Sulawesi Tengah'],
            ['INSTITUTION_CODE' => 'SMKN1MAKASSAR', 'NAME' => 'SMK Negeri 1 Makassar', 'ADDRESS' => 'Jl. SMK No. 1, Makassar', 'CITY' => 'Makassar', 'PROVINCE' => 'Sulawesi Selatan'],
            ['INSTITUTION_CODE' => 'SMAN1KENDARI', 'NAME' => 'SMA Negeri 1 Kendari', 'ADDRESS' => 'Jl. SMA No. 1, Kendari', 'CITY' => 'Kendari', 'PROVINCE' => 'Sulawesi Tenggara'],
            ['INSTITUTION_CODE' => 'SMKN1GORONTALO', 'NAME' => 'SMK Negeri 1 Gorontalo', 'ADDRESS' => 'Jl. SMK No. 1, Gorontalo', 'CITY' => 'Gorontalo', 'PROVINCE' => 'Gorontalo'],

            // Data sekolah di Papua
            ['INSTITUTION_CODE' => 'SMAN1JAYAPURA', 'NAME' => 'SMA Negeri 1 Jayapura', 'ADDRESS' => 'Jl. SMA No. 1, Jayapura', 'CITY' => 'Jayapura', 'PROVINCE' => 'Papua'],
            ['INSTITUTION_CODE' => 'SMKN1MANOKWARI', 'NAME' => 'SMK Negeri 1 Manokwari', 'ADDRESS' => 'Jl. SMK No. 1, Manokwari', 'CITY' => 'Manokwari', 'PROVINCE' => 'Papua Barat'],

            // Data sekolah swasta untuk contoh tambahan
            ['INSTITUTION_CODE' => 'SMKSWADAYA', 'NAME' => 'SMK Swadaya', 'ADDRESS' => 'Jl. Veteran No. 8, Bengkulu', 'CITY' => 'Bengkulu', 'PROVINCE' => 'Bengkulu'],
            ['INSTITUTION_CODE' => 'SMAPLUSSUMATERA', 'NAME' => 'SMA Plus Sumatra', 'ADDRESS' => 'Jl. Pahlawan No. 15, Pekanbaru', 'CITY' => 'Pekanbaru', 'PROVINCE' => 'Riau'],
            ['INSTITUTION_CODE' => 'SMKTEKNOLOGILAMPUNG', 'NAME' => 'SMK Teknologi Lampung', 'ADDRESS' => 'Jl. Teknologi No. 5, Bandar Lampung', 'CITY' => 'Bandar Lampung', 'PROVINCE' => 'Lampung'],
            ['INSTITUTION_CODE' => 'SMKHARAPANBANGSA', 'NAME' => 'SMA Harapan Bangsa', 'ADDRESS' => 'Jl. Kemerdekaan No. 12, Jambi', 'CITY' => 'Jambi', 'PROVINCE' => 'Jambi'],
            ['INSTITUTION_CODE' => 'SMKKREATIF', 'NAME' => 'SMK Kreatif', 'ADDRESS' => 'Jl. Seni No. 7, Pangkal Pinang', 'CITY' => 'Pangkal Pinang', 'PROVINCE' => 'Kepulauan Bangka Belitung']
        ];

        // Hapus semua data lama
        DB::table('mstr_schools')->delete();

        // Masukkan data baru satu per satu
        foreach ($schools as $school) {
            // Cek apakah kode institusi sudah ada
            $existing = DB::table('mstr_schools')
                ->where('INSTITUTION_CODE', $school['INSTITUTION_CODE'])
                ->first();

            if (!$existing) {
                DB::table('mstr_schools')->insert($school);
            }
        }

        $this->command->info('Data sekolah berhasil ditambahkan (' . count($schools) . ' records)');
    }
}