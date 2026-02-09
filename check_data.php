<?php

// Cek jumlah data di berbagai tabel terkait
echo "=== CEK DATA DI DATABASE ===\n";

// Cek jumlah data di mstr_schools
$schoolsCount = DB::table('mstr_schools')->count();
echo "Jumlah sekolah di mstr_schools: $schoolsCount\n";

// Cek jumlah data di trans_input_data
$inputDataCount = DB::table('trans_input_data')->count();
echo "Jumlah data di trans_input_data: $inputDataCount\n";

// Cek jumlah data di trans_input_data_schools_id
$linkCount = DB::table('trans_input_data_schools_id')->count();
echo "Jumlah link di trans_input_data_schools_id: $linkCount\n";

// Cek jumlah data di trans_input_data_person
$personCount = DB::table('trans_input_data_person')->count();
echo "Jumlah data di trans_input_data_person: $personCount\n";

// Cek jumlah data di trans_input_data_sponsorship
$sponsorshipCount = DB::table('trans_input_data_sponsorship')->count();
echo "Jumlah data di trans_input_data_sponsorship: $sponsorshipCount\n";

// Cek jumlah data di manual_entries
$manualEntriesCount = DB::table('manual_entries')->count();
echo "Jumlah data di manual_entries: $manualEntriesCount\n";

// Cek data spesifik dari trans_input_data
echo "\n=== CONTOH DATA DARI trans_input_data ===\n";
$inputData = DB::table('trans_input_data')->select('Input_Data_Id', 'Input_Data_Type', 'Promotion_Name', 'Event_Start_Date', 'Note')->get();
foreach ($inputData as $data) {
    echo "ID: {$data->Input_Data_Id}, Type: {$data->Input_Data_Type}, Name: {$data->Promotion_Name}, Date: {$data->Event_Start_Date}\n";
    if ($data->Note) {
        echo "  Note: {$data->Note}\n";
    }
}

// Cek data dari mstr_schools
echo "\n=== CONTOH DATA DARI mstr_schools ===\n";
$schools = DB::table('mstr_schools')->select('INSTITUTION_CODE', 'NAME', 'PROVINCE', 'CITY')->limit(5)->get();
foreach ($schools as $school) {
    echo "Code: {$school->INSTITUTION_CODE}, Name: {$school->NAME}, Province: {$school->PROVINCE}, City: {$school->CITY}\n";
}

// Cek data dari trans_input_data_schools_id
echo "\n=== CONTOH DATA DARI trans_input_data_schools_id ===\n";
$links = DB::table('trans_input_data_schools_id')->select('Input_Data_Id', 'School_Id')->limit(5)->get();
foreach ($links as $link) {
    echo "Input_Data_Id: {$link->Input_Data_Id}, School_Id: {$link->School_Id}\n";
}