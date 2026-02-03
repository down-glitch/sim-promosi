<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoadshowSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama terlebih dahulu
        DB::table('trans_input_data_sponsorship')->truncate();
        DB::table('trans_input_data_department')->truncate();
        DB::table('trans_input_data_person')->truncate();
        DB::table('trans_input_data_schools_id')->truncate();
        DB::table('trans_input_data')->truncate();

        // Ambil beberapa sekolah dari database
        $schools = DB::table('mstr_schools')->limit(3)->get();

        if ($schools->isEmpty()) {
            echo "Tidak ada sekolah di database!\n";
            return;
        }

        // Cek apakah ada department
        $departments = DB::table('mstr_department')->limit(3)->get();
        
        if ($departments->isEmpty()) {
            echo "Tidak ada department di database. Membuat department sample...\n";
            // Buat department sample jika tidak ada
            foreach (['Teknik Informatika', 'Administrasi Bisnis', 'Akuntansi'] as $dept) {
                DB::table('mstr_department')->insert([
                    'DEPARTMENT_NAME' => $dept,
                    'FACULTY' => 'Fakultas Teknik',
                    'Created_By' => 1,
                    'Modified_By' => 1,
                    'Created_Date' => now(),
                    'Modified_Date' => now(),
                ]);
            }
            $departments = DB::table('mstr_department')->limit(3)->get();
        }

        $now = now();
        
        echo "Creating test data...\n";
        echo "Schools: " . $schools->count() . "\n";
        echo "Departments: " . $departments->count() . "\n";

        // Buat 1 event roadshow
        $inputDataId = DB::table('trans_input_data')->insertGetId([
            'Input_Data_Type' => 1,
            'Promotion_Name' => 'Roadshow Promosi Jakarta',
            'Event_Start_Date' => '2026-01-15',
            'Event_End_Date' => '2026-01-15',
            'Note' => 'Event roadshow promosi Jakarta',
            'Created_By' => 1,
            'Modified_By' => 1,
            'Created_Date' => $now,
            'Modified_Date' => $now,
        ]);

        echo "Created Input_Data_Id: $inputDataId\n";

        $penanggungjawabs = ['Budi Santoso', 'Siti Nurhaliza', 'Ahmad Hidayat'];
        $alumnis = ['150', '200', '180'];

        // Tambahkan sekolah-sekolah
        foreach ($schools as $index => $school) {
            // Link sekolah dengan event
            $schoolLinkId = DB::table('trans_input_data_schools_id')->insertGetId([
                'Input_Data_Id' => $inputDataId,
                'School_Id' => $school->INSTITUTION_CODE,
                'Created_By' => 1,
                'Modified_By' => 1,
                'Created_Date' => $now,
                'Modified_Date' => $now,
            ]);

            echo "Added School: {$school->NAME} (School_Id: {$school->INSTITUTION_CODE})\n";

            // Tambah penanggungjawab
            DB::table('trans_input_data_person')->insert([
                'Input_Data_Id' => $inputDataId,
                'Name' => $penanggungjawabs[$index % count($penanggungjawabs)],
                'Created_By' => 1,
                'Modified_By' => 1,
                'Created_Date' => $now,
                'Modified_Date' => $now,
            ]);

            echo "  Added Person: {$penanggungjawabs[$index % count($penanggungjawabs)]}\n";

            // Tambah 3 department/prodi
            foreach ($departments as $dept) {
                $deptInsert = DB::table('trans_input_data_department')->insert([
                    'Input_Data_Id' => $inputDataId,
                    'Department_Id' => $dept->DEPARTMENT_ID,
                    'Created_By' => 1,
                    'Modified_By' => 1,
                    'Created_Date' => $now,
                    'Modified_Date' => $now,
                ]);
                
                if ($deptInsert) {
                    echo "    Added Department: {$dept->DEPARTMENT_NAME} (ID: {$dept->DEPARTMENT_ID})\n";
                }
            }

            // Tambah alumni
            $alumniInsert = DB::table('trans_input_data_sponsorship')->insert([
                'Input_Data_Id' => $inputDataId,
                'Sponsorship_Name' => $alumnis[$index % count($alumnis)],
                'Amount' => 0,
                'Description' => 'Data alumni',
                'Created_By' => 1,
                'Modified_By' => 1,
                'Created_Date' => $now,
                'Modified_Date' => $now,
            ]);

            if ($alumniInsert) {
                echo "  Added Alumni: {$alumnis[$index % count($alumnis)]}\n";
            }
        }

        echo "\n✓ Seeder berhasil! Data roadshow telah ditambahkan ke database.\n";
        
        // Verifikasi data
        echo "\nVerifikasi:\n";
        echo "- trans_input_data: " . DB::table('trans_input_data')->count() . " records\n";
        echo "- trans_input_data_schools_id: " . DB::table('trans_input_data_schools_id')->count() . " records\n";
        echo "- trans_input_data_person: " . DB::table('trans_input_data_person')->count() . " records\n";
        echo "- trans_input_data_department: " . DB::table('trans_input_data_department')->count() . " records\n";
        echo "- trans_input_data_sponsorship: " . DB::table('trans_input_data_sponsorship')->count() . " records\n";
    }
}



