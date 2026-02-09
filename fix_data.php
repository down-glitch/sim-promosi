<?php

echo "Memperbaiki data yang tidak cocok...\n";

// Ambil semua data dari trans_input_data_schools_id yang tidak valid
$invalidLinks = DB::table('trans_input_data_schools_id as tdsi')
    ->leftJoin('mstr_schools as ms', 'tdsi.School_Id', '=', 'ms.INSTITUTION_CODE')
    ->whereNull('ms.INSTITUTION_CODE') // Yang tidak punya pasangan di mstr_schools
    ->select('tdsi.Input_Data_Id', 'tdsi.School_Id', 'tdsi.Id as link_id')
    ->get();

echo "Ditemukan " . count($invalidLinks) . " link yang tidak valid\n";

foreach ($invalidLinks as $link) {
    // Ambil data dari trans_input_data untuk mendapatkan informasi sekolah dari Note
    $inputData = DB::table('trans_input_data')
        ->where('Input_Data_Id', $link->Input_Data_Id)
        ->first();
        
    if ($inputData && $inputData->Note) {
        $note = json_decode($inputData->Note, true);
        if ($note && isset($note['sekolah'])) {
            $institutionCode = $note['sekolah'];
            
            // Cek apakah INSTITUTION_CODE ini ada di mstr_schools
            $schoolExists = DB::table('mstr_schools')
                ->where('INSTITUTION_CODE', $institutionCode)
                ->exists();
                
            if ($schoolExists) {
                // Update link dengan INSTITUTION_CODE yang benar
                DB::table('trans_input_data_schools_id')
                    ->where('Id', $link->link_id)
                    ->update(['School_Id' => $institutionCode]);
                    
                echo "Memperbaiki link {$link->Input_Data_Id}: {$link->School_Id} -> {$institutionCode}\n";
            } else {
                echo "INSTITUTION_CODE {$institutionCode} tidak ditemukan di mstr_schools untuk Input_Data_Id {$link->Input_Data_Id}\n";
                
                // Coba cari sekolah dengan nama yang mirip di mstr_schools
                $similarSchool = DB::table('mstr_schools')
                    ->where('NAME', 'LIKE', "%{$institutionCode}%")
                    ->orWhere('NAME', 'LIKE', str_replace(' ', '%', $institutionCode))
                    ->first();
                    
                if ($similarSchool) {
                    // Update link dengan INSTITUTION_CODE yang ditemukan
                    DB::table('trans_input_data_schools_id')
                        ->where('Id', $link->link_id)
                        ->update(['School_Id' => $similarSchool->INSTITUTION_CODE]);
                        
                    echo "Memperbaiki link {$link->Input_Data_Id}: {$link->School_Id} -> {$similarSchool->INSTITUTION_CODE} (berdasarkan nama yang mirip)\n";
                }
            }
        }
    }
}

echo "Perbaikan selesai.\n";