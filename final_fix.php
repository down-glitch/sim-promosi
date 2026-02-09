<?php

echo "Memperbaiki link-link yang masih salah...\n";

// Perbaiki link berdasarkan note yang sebenarnya
$linksToFix = [
    ['Input_Data_Id' => 4, 'correct_code' => 'SMAN1YOGYAKARTA'],
    ['Input_Data_Id' => 6, 'correct_code' => 'SMAN1SRAGEN'],
    ['Input_Data_Id' => 7, 'correct_code' => 'SMKN1JAKARTAPUSAT'],
    ['Input_Data_Id' => 8, 'correct_code' => 'SMAN1YOGYAKARTA']
];

foreach ($linksToFix as $fix) {
    $inputData = DB::table('trans_input_data')
        ->where('Input_Data_Id', $fix['Input_Data_Id'])
        ->first();
        
    if ($inputData && $inputData->Note) {
        $note = json_decode($inputData->Note, true);
        if ($note && isset($note['sekolah'])) {
            $currentLinkId = DB::table('trans_input_data_schools_id')
                ->where('Input_Data_Id', $fix['Input_Data_Id'])
                ->first();
                
            if ($currentLinkId) {
                DB::table('trans_input_data_schools_id')
                    ->where('Input_Data_Id', $fix['Input_Data_Id'])
                    ->update(['School_Id' => $fix['correct_code']]);
                    
                echo "Memperbaiki link {$fix['Input_Data_Id']}: {$currentLinkId->School_Id} -> {$fix['correct_code']} (harusnya: {$note['sekolah']})\n";
            }
        }
    }
}

// Perbaiki link untuk Input_Data_Id 3
$inputData3 = DB::table('trans_input_data')
    ->where('Input_Data_Id', 3)
    ->first();
    
if ($inputData3 && $inputData3->Note === 'oke') {
    // Coba cocokkan dengan sekolah yang memiliki 'oke' di nama atau kode
    $similarSchool = DB::table('mstr_schools')
        ->where('NAME', 'LIKE', '%oke%')
        ->orWhere('INSTITUTION_CODE', 'LIKE', '%oke%')
        ->first();
        
    if ($similarSchool) {
        DB::table('trans_input_data_schools_id')
            ->where('Input_Data_Id', 3)
            ->update(['School_Id' => $similarSchool->INSTITUTION_CODE]);
            
        echo "Memperbaiki link 3: INST004 -> {$similarSchool->INSTITUTION_CODE} (berdasarkan 'oke')\n";
    } else {
        // Jika tidak ditemukan, gunakan sekolah default
        $defaultSchool = DB::table('mstr_schools')->first();
        if ($defaultSchool) {
            DB::table('trans_input_data_schools_id')
                ->where('Input_Data_Id', 3)
                ->update(['School_Id' => $defaultSchool->INSTITUTION_CODE]);
                
            echo "Memperbaiki link 3: INST004 -> {$defaultSchool->INSTITUTION_CODE} (sekolah default)\n";
        }
    }
}

echo "Perbaikan link yang tersisa selesai.\n";