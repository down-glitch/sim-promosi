<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$school_id = 'INST001';

// Simulate API response
$history = DB::table('trans_input_data_schools_id as tdsi')
    ->join('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
    ->leftJoin('trans_input_data_sponsorship as tds', 'td.Input_Data_Id', '=', 'tds.Input_Data_Id')
    ->where('tdsi.School_Id', $school_id)
    ->select('td.Input_Data_Id', 'td.Promotion_Name', 'td.Event_Start_Date', 'tds.Sponsorship_Name', 'tds.Amount')
    ->orderBy('td.Event_Start_Date', 'desc')
    ->limit(10)
    ->get()
    ->map(function($item) {
        $alumni = 0;
        if (!empty($item->Sponsorship_Name)) {
            if (is_numeric($item->Sponsorship_Name)) {
                $alumni = (int)$item->Sponsorship_Name;
            } else if ((int)$item->Amount > 0) {
                $alumni = (int)$item->Amount;
            }
        }
        
        return [
            'Input_Data_Id' => $item->Input_Data_Id,
            'Promotion_Name' => $item->Promotion_Name,
            'Event_Start_Date' => $item->Event_Start_Date,
            'alumni_count' => $alumni
        ];
    })
    ->toArray();

echo "API Response JSON:\n";
echo json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
