<?php

echo "Memperbaiki data dengan pencarian yang lebih akurat...\n";

// Ambil semua data dari trans_input_data_schools_id yang tidak valid
$invalidLinks = DB::table('trans_input_data_schools_id as tdsi')
    ->leftJoin('mstr_schools as ms', 'tdsi.School_Id', '=', 'ms.INSTITUTION_CODE')
    ->whereNull('ms.INSTITUTION_CODE') // Yang tidak punya pasangan di mstr_schools
    ->select('tdsi.Input_Data_Id', 'tdsi.School_Id')
    ->get();

echo "Ditemukan " . count($invalidLinks) . " link yang masih tidak valid\n";

foreach ($invalidLinks as $link) {
    // Ambil data dari trans_input_data untuk mendapatkan informasi sekolah dari Note
    $inputData = DB::table('trans_input_data')
        ->where('Input_Data_Id', $link->Input_Data_Id)
        ->first();
        
    if ($inputData && $inputData->Note) {
        $note = json_decode($inputData->Note, true);
        if ($note && isset($note['sekolah'])) {
            $schoolName = $note['sekolah'];
            
            // Cari sekolah yang persis sama namanya
            $exactMatch = DB::table('mstr_schools')
                ->where('NAME', $schoolName)
                ->first();
                
            if ($exactMatch) {
                DB::table('trans_input_data_schools_id')
                    ->where('Input_Data_Id', $link->Input_Data_Id)
                    ->where('School_Id', $link->School_Id)
                    ->update(['School_Id' => $exactMatch->INSTITUTION_CODE]);
                    
                echo "Memperbaiki {$link->Input_Data_Id}: {$link->School_Id} -> {$exactMatch->INSTITUTION_CODE} (cocok eksak: {$schoolName})\n";
            } else {
                // Cari dengan LIKE yang lebih spesifik
                $similarMatch = DB::table('mstr_schools')
                    ->where('NAME', 'LIKE', "%{$schoolName}%")
                    ->orWhere(DB::raw('REPLACE(NAME, " ", "")'), 'LIKE', '%' . str_replace(' ', '', $schoolName) . '%')
                    ->orWhere(DB::raw('REPLACE(NAME, "Negeri", "N")'), 'LIKE', '%' . str_replace('Negeri', 'N', $schoolName) . '%')
                    ->first();
                    
                if ($similarMatch) {
                    DB::table('trans_input_data_schools_id')
                        ->where('Input_Data_Id', $link->Input_Data_Id)
                        ->where('School_Id', $link->School_Id)
                        ->update(['School_Id' => $similarMatch->INSTITUTION_CODE]);
                        
                    echo "Memperbaiki {$link->Input_Data_Id}: {$link->School_Id} -> {$similarMatch->INSTITUTION_CODE} (cocok mirip: {$schoolName} -> {$similarMatch->NAME})\n";
                } else {
                    echo "Tidak ditemukan kecocokan untuk: {$schoolName} (Input_Data_Id: {$link->Input_Data_Id})\n";
                }
            }
        }
    }
}

echo "Perbaikan akurat selesai.\n";