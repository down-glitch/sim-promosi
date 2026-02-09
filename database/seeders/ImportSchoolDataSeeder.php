<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImportSchoolDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama terlebih dahulu
        \DB::table('mstr_schools')->truncate();

        // Baca file SQL yang berisi data sekolah
        $sqlFile = base_path('daftar-sekolah.sql');

        if (!file_exists($sqlFile)) {
            $this->command->error('File daftar-sekolah.sql tidak ditemukan di root direktori.');
            return;
        }

        $sqlContent = file_get_contents($sqlFile);

        // Eksekusi perintah SQL
        \DB::unprepared($sqlContent);

        $this->command->info('Data sekolah berhasil diimport dari file SQL.');
    }
}
