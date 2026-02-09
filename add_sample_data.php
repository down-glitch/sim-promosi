<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

// Tambahkan data ke mstr_schools dari file daftar-sekolah.sql
$sql = file_get_contents(base_path('daftar-sekolah.sql'));

// Ekstrak hanya bagian INSERT INTO mstr_schools
preg_match_all('/INSERT INTO mstr_schools \(.*?\) VALUES (.*?;)/s', $sql, $matches);

if (isset($matches[0][0])) {
    // Kita hanya ambil bagian VALUES-nya dan pisahkan tiap baris
    $insertStmt = $matches[0][0];
    
    // Ganti INSERT INTO mstr_schools (...) VALUES dengan perintah individual
    $valuesPart = preg_replace('/^INSERT INTO mstr_schools \(.*?\) VALUES /', '', $insertStmt);
    
    // Pisahkan setiap baris data (dipisahkan dengan ),(
    $individualValues = preg_split('/\),\s*\(/', $valuesPart);
    
    foreach ($individualValues as $index => $value) {
        // Bersihkan dari kurung dan titik koma
        $value = trim($value, " ();\n\r\t");
        
        // Jika ini bukan baris terakhir, tambahkan kembali kurung
        if ($index === count($individualValues) - 1) {
            $value = '(' . $value . ');';
        } else {
            $value = '(' . $value . '),';
        }
        
        // Ekstrak data dari value
        preg_match_all("/'(.*?)'(?=,|\)|;)/", $value, $dataMatches);
        $data = $dataMatches[1];
        
        if (count($data) >= 5) {
            DB::table('mstr_schools')->insert([
                'INSTITUTION_CODE' => $data[0],
                'NAME' => $data[1],
                'ADDRESS' => $data[2],
                'CITY' => $data[3],
                'PROVINCE' => $data[4],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}

// Tambahkan data contoh ke trans_input_data dan tabel terkait
$inputDataId = DB::table('trans_input_data')->insertGetId([
    'Input_Data_Type' => 1, // Roadshow
    'Promotion_Name' => 'Roadshow Edukasi 2026',
    'Event_Start_Date' => '2026-02-10',
    'Event_End_Date' => '2026-02-12',
    'Note' => json_encode(['provinsi' => 'Jawa Barat', 'kabupaten' => 'Bandung', 'sekolah' => 'SMKN1BANDUNG']),
    'Created_By' => 'admin',
    'Modified_By' => 'admin',
    'Created_Date' => now(),
    'Modified_Date' => now()
]);

// Tambahkan ke trans_input_data_schools_id
DB::table('trans_input_data_schools_id')->insert([
    'Input_Data_Id' => $inputDataId,
    'School_Id' => 'SMKN1BANDUNG',
    'Created_By' => 'admin',
    'Modified_By' => 'admin',
    'Created_Date' => now(),
    'Modified_Date' => now()
]);

// Tambahkan ke trans_input_data_person
DB::table('trans_input_data_person')->insert([
    'Input_Data_Id' => $inputDataId,
    'Name' => 'Budi Santoso',
    'Created_By' => 'admin',
    'Modified_By' => 'admin',
    'Created_Date' => now(),
    'Modified_Date' => now()
]);

// Tambahkan ke trans_input_data_sponsorship
DB::table('trans_input_data_sponsorship')->insert([
    'Input_Data_Id' => $inputDataId,
    'Sponsorship_Name' => '50',
    'Amount' => 50,
    'Description' => 'Jumlah alumni yang hadir',
    'Created_By' => 'admin',
    'Modified_By' => 'admin',
    'Created_Date' => now(),
    'Modified_Date' => now()
]);

echo "Data contoh berhasil ditambahkan ke database.";