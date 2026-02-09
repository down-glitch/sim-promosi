<?php

echo "Memperbaiki link-link berdasarkan data yang baru ditambahkan...\n";

// Perbaiki link-link berdasarkan note
$linksToFix = DB::table('trans_input_data_schools_id as tdsi')
    ->leftJoin('mstr_schools as ms', 'tdsi.School_Id', '=', 'ms.INSTITUTION_CODE')
    ->whereNull('ms.INSTITUTION_CODE') // Yang tidak punya pasangan di mstr_schools
    ->select('tdsi.Input_Data_Id', 'tdsi.School_Id')
    ->get();

foreach ($linksToFix as $link) {
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
                ->orWhere('INSTITUTION_CODE', $schoolName)
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
        }
    }
}

// Juga perbaiki link untuk INST004 berdasarkan note
$inst004Records = DB::table('trans_input_data_schools_id')
    ->where('School_Id', 'INST004')
    ->get();

foreach ($inst004Records as $record) {
    $inputData = DB::table('trans_input_data')
        ->where('Input_Data_Id', $record->Input_Data_Id)
        ->first();
        
    if ($inputData && $inputData->Note) {
        // Jika note adalah string biasa (bukan JSON), kita coba cocokkan
        if (!is_object(json_decode($inputData->Note)) && !is_array(json_decode($inputData->Note))) {
            // Ini mungkin nama sekolah
            $schoolName = $inputData->Note;
            
            $correctSchool = DB::table('mstr_schools')
                ->where('NAME', 'LIKE', "%{$schoolName}%")
                ->orWhere('INSTITUTION_CODE', 'LIKE', "%{$schoolName}%")
                ->first();
                
            if ($correctSchool) {
                DB::table('trans_input_data_schools_id')
                    ->where('Input_Data_Id', $record->Input_Data_Id)
                    ->where('School_Id', 'INST004')
                    ->update(['School_Id' => $correctSchool->INSTITUTION_CODE]);
                    
                echo "Memperbaiki INST004 {$record->Input_Data_Id}: INST004 -> {$correctSchool->INSTITUTION_CODE} (dari note sederhana: {$schoolName})\n";
            }
        }
    }
}

echo "Perbaikan link selesai.\n";