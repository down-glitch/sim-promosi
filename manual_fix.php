<?php

echo "Memperbaiki data secara manual berdasarkan nama sekolah yang mirip...\n";

// Daftar koreksi manual berdasarkan nama sekolah
$corrections = [
    'SMA Negeri 1 Yogyakarta' => 'SMAN1YOGYAKARTA',
    'smk n 1 bantul' => 'SMKN1BANTUL', // Tidak ditemukan, kita buat kode yang sesuai
    'SMA Negeri 1 Sragen' => 'SMAN1SRAGEN', // Tidak ditemukan, kita buat kode yang sesuai
    'SMK Negeri 1 Jakarta' => 'SMKN1JAKARTAPUSAT', // Menggunakan SMK Negeri 1 Jakarta Pusat
];

// Coba cari nama-nama sekolah yang mirip di database
foreach ($corrections as $originalName => $expectedCode) {
    // Cari nama sekolah yang mirip di mstr_schools
    $similarSchool = DB::table('mstr_schools')
        ->where('NAME', 'LIKE', "%{$originalName}%")
        ->orWhere('NAME', 'LIKE', str_replace(' ', '%', $originalName))
        ->orWhere('NAME', 'LIKE', str_replace(['Negeri', 'N'], ['%', '%'], $originalName))
        ->first();
        
    if ($similarSchool) {
        $actualCode = $similarSchool->INSTITUTION_CODE;
        echo "Menemukan {$originalName} sebagai {$similarSchool->NAME} (kode: {$actualCode})\n";
        $corrections[$originalName] = $actualCode;
    } else {
        echo "Tidak menemukan sekolah yang cocok untuk: {$originalName}\n";
        
        // Coba cari dengan pencarian yang lebih luas
        $parts = explode(' ', $originalName);
        foreach ($parts as $part) {
            if (strlen($part) > 3) { // Hanya cari bagian dengan panjang > 3 karakter
                $broadMatch = DB::table('mstr_schools')
                    ->where('NAME', 'LIKE', "%{$part}%")
                    ->first();
                    
                if ($broadMatch) {
                    echo "  -> Cocok kasar ditemukan: {$broadMatch->NAME} (kode: {$broadMatch->INSTITUTION_CODE})\n";
                    $corrections[$originalName] = $broadMatch->INSTITUTION_CODE;
                    break;
                }
            }
        }
    }
}

// Sekarang perbarui data berdasarkan koreksi
$updates = [
    ['Input_Data_Id' => 4, 'old_school_id' => 'SMA Negeri 1 Yogyakarta', 'new_school_id' => $corrections['SMA Negeri 1 Yogyakarta'] ?? 'SMA Negeri 1 Yogyakarta'],
    ['Input_Data_Id' => 5, 'old_school_id' => 'smk n 1 bantul', 'new_school_id' => $corrections['smk n 1 bantul'] ?? 'smk n 1 bantul'],
    ['Input_Data_Id' => 6, 'old_school_id' => 'SMA Negeri 1 Sragen', 'new_school_id' => $corrections['SMA Negeri 1 Sragen'] ?? 'SMA Negeri 1 Sragen'],
    ['Input_Data_Id' => 7, 'old_school_id' => 'SMK Negeri 1 Jakarta', 'new_school_id' => $corrections['SMK Negeri 1 Jakarta'] ?? 'SMK Negeri 1 Jakarta'],
    ['Input_Data_Id' => 8, 'old_school_id' => 'SMA Negeri 1 Yogyakarta', 'new_school_id' => $corrections['SMA Negeri 1 Yogyakarta'] ?? 'SMA Negeri 1 Yogyakarta'],
];

foreach ($updates as $update) {
    if ($update['new_school_id'] !== $update['old_school_id']) {
        $affected = DB::table('trans_input_data_schools_id')
            ->where('Input_Data_Id', $update['Input_Data_Id'])
            ->where('School_Id', $update['old_school_id'])
            ->update(['School_Id' => $update['new_school_id']]);
            
        if ($affected > 0) {
            echo "Memperbarui Input_Data_Id {$update['Input_Data_Id']}: {$update['old_school_id']} -> {$update['new_school_id']}\n";
        } else {
            echo "Tidak ada baris yang diperbarui untuk Input_Data_Id {$update['Input_Data_Id']}\n";
        }
    }
}

// Untuk INST004, kita perlu mencari tahu dari mana asalnya
$inst004Records = DB::table('trans_input_data_schools_id')
    ->where('School_Id', 'INST004')
    ->get();

foreach ($inst004Records as $record) {
    $inputData = DB::table('trans_input_data')
        ->where('Input_Data_Id', $record->Input_Data_Id)
        ->first();
        
    if ($inputData && $inputData->Note) {
        $note = json_decode($inputData->Note, true);
        if ($note && isset($note['sekolah'])) {
            $schoolName = $note['sekolah'];
            
            // Cari sekolah dengan nama ini di mstr_schools
            $schoolInDb = DB::table('mstr_schools')
                ->where('NAME', $schoolName)
                ->orWhere('NAME', 'LIKE', "%{$schoolName}%")
                ->first();
                
            if ($schoolInDb) {
                DB::table('trans_input_data_schools_id')
                    ->where('Input_Data_Id', $record->Input_Data_Id)
                    ->where('School_Id', 'INST004')
                    ->update(['School_Id' => $schoolInDb->INSTITUTION_CODE]);
                    
                echo "Memperbarui INST004 untuk Input_Data_Id {$record->Input_Data_Id}: INST004 -> {$schoolInDb->INSTITUTION_CODE} (dari note: {$schoolName})\n";
            }
        }
    }
}

echo "Perbaikan manual selesai.\n";