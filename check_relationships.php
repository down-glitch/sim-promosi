<?php

echo "=== PERBANDINGAN DATA SCHOOL_ID ===\n";

// Cek School_Id dari trans_input_data_schools_id
$schoolIds = DB::table('trans_input_data_schools_id')->select('Input_Data_Id', 'School_Id')->get();
echo "Data di trans_input_data_schools_id:\n";
foreach ($schoolIds as $link) {
    $isValid = DB::table('mstr_schools')->where('INSTITUTION_CODE', $link->School_Id)->exists();
    echo "Input_Data_Id: {$link->Input_Data_Id}, School_Id: {$link->School_Id}, Valid: " . ($isValid ? 'Ya' : 'Tidak') . "\n";
}

echo "\n=== DATA DENGAN INPUT_DATA_TYPE = 1 ===\n";
$type1Data = DB::table('trans_input_data')
    ->where('Input_Data_Type', 1)
    ->select('Input_Data_Id', 'Promotion_Name', 'Event_Start_Date', 'Note')
    ->get();
    
foreach ($type1Data as $data) {
    echo "ID: {$data->Input_Data_Id}, Name: {$data->Promotion_Name}, Date: {$data->Event_Start_Date}\n";
    if ($data->Note) {
        echo "  Note: {$data->Note}\n";
    }
    
    // Cek apakah ada link ke sekolah
    $schoolLink = DB::table('trans_input_data_schools_id')
        ->where('Input_Data_Id', $data->Input_Data_Id)
        ->first();
        
    if ($schoolLink) {
        echo "  Linked School_Id: {$schoolLink->School_Id}\n";
        $schoolInfo = DB::table('mstr_schools')
            ->where('INSTITUTION_CODE', $schoolLink->School_Id)
            ->first();
            
        if ($schoolInfo) {
            echo "  School Info: {$schoolInfo->NAME}, {$schoolInfo->PROVINCE}, {$schoolInfo->CITY}\n";
        } else {
            echo "  School Info: Tidak ditemukan di mstr_schools!\n";
        }
    }
    echo "\n";
}

echo "\n=== DATA DARI MANUAL_ENTRIES ===\n";
$manualData = DB::table('manual_entries')->get();
echo "Jumlah data di manual_entries: " . count($manualData) . "\n";
foreach ($manualData as $entry) {
    echo "Province: {$entry->province}, City: {$entry->city}, School: {$entry->school_name}\n";
}