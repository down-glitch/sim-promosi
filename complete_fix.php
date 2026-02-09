<?php

echo "Menambahkan data sekolah yang hilang dan memperbaiki link...\n";

// Tambahkan sekolah-sekolah yang hilang ke tabel mstr_schools
$missingSchools = [
    [
        'INSTITUTION_CODE' => 'SMAN1YOGYAKARTA',
        'NAME' => 'SMA Negeri 1 Yogyakarta',
        'ADDRESS' => 'Jl. Tentara Rakyat Mataram No.1, Ngupasan, Gondomanan, Kota Yogyakarta',
        'CITY' => 'Yogyakarta',
        'PROVINCE' => 'Daerah Istimewa Yogyakarta'
    ],
    [
        'INSTITUTION_CODE' => 'SMKN1BANTUL',
        'NAME' => 'SMK Negeri 1 Bantul',
        'ADDRESS' => 'Jl. Tamansiswa No.43, Trirenggo, Sewon, Bantul',
        'CITY' => 'Bantul',
        'PROVINCE' => 'Daerah Istimewa Yogyakarta'
    ],
    [
        'INSTITUTION_CODE' => 'SMAN1SRAGEN',
        'NAME' => 'SMA Negeri 1 Sragen',
        'ADDRESS' => 'Jl. Raya Sukowati No.KM. 5, Sragen',
        'CITY' => 'Sragen',
        'PROVINCE' => 'Jawa Tengah'
    ],
    [
        'INSTITUTION_CODE' => 'SMKN1JAKARTAPUSAT',
        'NAME' => 'SMK Negeri 1 Jakarta Pusat',
        'ADDRESS' => 'Jl. KH. Mas Mansyur No.116, Jakarta Pusat',
        'CITY' => 'Jakarta Pusat',
        'PROVINCE' => 'DKI Jakarta'
    ]
];

foreach ($missingSchools as $school) {
    $existing = DB::table('mstr_schools')
        ->where('INSTITUTION_CODE', $school['INSTITUTION_CODE'])
        ->first();
        
    if (!$existing) {
        DB::table('mstr_schools')->insert($school);
        echo "Menambahkan sekolah: {$school['NAME']} ({$school['INSTITUTION_CODE']})\n";
    } else {
        echo "Sekolah {$school['INSTITUTION_CODE']} sudah ada\n";
    }
}

// Sekarang perbaiki link-link yang salah
$corrections = [
    ['old_code' => 'INST004', 'note_school' => 'SMA Harapan Bangsa', 'correct_code' => 'SMKHARAPANBANGSA'],
    ['old_code' => 'SMA Negeri 1 Yogyakarta', 'note_school' => 'SMA Negeri 1 Yogyakarta', 'correct_code' => 'SMAN1YOGYAKARTA'],
    ['old_code' => 'smk n 1 bantul', 'note_school' => 'smk n 1 bantul', 'correct_code' => 'SMKN1BANTUL'],
    ['old_code' => 'SMA Negeri 1 Sragen', 'note_school' => 'SMA Negeri 1 Sragen', 'correct_code' => 'SMAN1SRAGEN'],
    ['old_code' => 'SMK Negeri 1 Jakarta', 'note_school' => 'SMK Negeri 1 Jakarta', 'correct_code' => 'SMKN1JAKARTAPUSAT'],
    // Untuk SMAN1BANDAACEH yang salah ditetapkan, kita perlu perbaiki berdasarkan note
];

// Perbaiki berdasarkan data aslinya
$invalidLinks = DB::table('trans_input_data_schools_id as tdsi')
    ->leftJoin('mstr_schools as ms', 'tdsi.School_Id', '=', 'ms.INSTITUTION_CODE')
    ->whereNull('ms.INSTITUTION_CODE') // Yang tidak punya pasangan di mstr_schools
    ->select('tdsi.Input_Data_Id', 'tdsi.School_Id')
    ->get();

foreach ($invalidLinks as $link) {
    // Ambil data dari trans_input_data untuk mendapatkan informasi sekolah dari Note
    $inputData = DB::table('trans_input_data')
        ->where('Input_Data_Id', $link->Input_Data_Id)
        ->first();
        
    if ($inputData && $inputData->Note) {
        $note = json_decode($inputData->Note, true);
        if ($note && isset($note['sekolah'])) {
            $schoolName = $note['sekolah'];
            
            // Cari INSTITUTION_CODE yang benar berdasarkan nama sekolah
            $correctSchool = DB::table('mstr_schools')
                ->where('NAME', $schoolName)
                ->orWhere('INSTITUTION_CODE', $schoolName) // Kadang note berisi institution code
                ->first();
                
            if ($correctSchool) {
                DB::table('trans_input_data_schools_id')
                    ->where('Input_Data_Id', $link->Input_Data_Id)
                    ->where('School_Id', $link->School_Id)
                    ->update(['School_Id' => $correctSchool->INSTITUTION_CODE]);
                    
                echo "Memperbaiki link {$link->Input_Data_Id}: {$link->School_Id} -> {$correctSchool->INSTITUTION_CODE} (dari note: {$schoolName})\n";
            } else {
                echo "Tidak menemukan sekolah untuk note: {$schoolName} (Input_Data_Id: {$link->Input_Data_Id})\n";
            }
        } else {
            // Jika tidak ada note, coba cocokkan berdasarkan nama yang ada
            $similarSchool = DB::table('mstr_schools')
                ->where('NAME', 'LIKE', "%{$link->School_Id}%")
                ->orWhere('INSTITUTION_CODE', 'LIKE', "%{$link->School_Id}%")
                ->first();
                
            if ($similarSchool) {
                DB::table('trans_input_data_schools_id')
                    ->where('Input_Data_Id', $link->Input_Data_Id)
                    ->where('School_Id', $link->School_Id)
                    ->update(['School_Id' => $similarSchool->INSTITUTION_CODE]);
                    
                echo "Memperbaiki link {$link->Input_Data_Id}: {$link->School_Id} -> {$similarSchool->INSTITUTION_CODE} (berdasarkan kemiripan)\n";
            }
        }
    }
}

echo "Perbaikan lengkap selesai.\n";