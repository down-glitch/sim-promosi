<?php
// Quick test of getSchoolHistory API

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Test for INST001
$school_id = 'INST001';

echo "Testing getSchoolHistory() for school: $school_id\n";
echo "================================================\n\n";

// Step 1: Check if school exists in trans_input_data_schools_id
echo "Step 1: Check if school linked to events\n";
$schoolLinks = DB::table('trans_input_data_schools_id')
    ->where('School_Id', $school_id)
    ->get();

echo "Found " . count($schoolLinks) . " event links for this school\n";
foreach ($schoolLinks as $link) {
    echo "  - Input_Data_Id: {$link->Input_Data_Id}\n";
}

echo "\n";

// Step 2: Get event details
echo "Step 2: Get event details\n";
$events = DB::table('trans_input_data_schools_id as tdsi')
    ->join('trans_input_data as td', 'tdsi.Input_Data_Id', '=', 'td.Input_Data_Id')
    ->where('tdsi.School_Id', $school_id)
    ->select(
        'td.Input_Data_Id',
        'td.Promotion_Name',
        'td.Event_Start_Date'
    )
    ->orderBy('td.Event_Start_Date', 'desc')
    ->distinct('td.Input_Data_Id')
    ->limit(10)
    ->get();

echo "Found " . count($events) . " distinct events\n";
foreach ($events as $event) {
    echo "  - Event ID: {$event->Input_Data_Id}, Name: {$event->Promotion_Name}, Date: {$event->Event_Start_Date}\n";
}

echo "\n";

// Step 3: Get alumni data per event
echo "Step 3: Get alumni data per event\n";
foreach ($events as $event) {
    $sponsorships = DB::table('trans_input_data_sponsorship')
        ->where('Input_Data_Id', $event->Input_Data_Id)
        ->get();
    
    echo "  Event ID {$event->Input_Data_Id}:\n";
    echo "    Found " . count($sponsorships) . " sponsorship records\n";
    
    $totalAlumni = 0;
    foreach ($sponsorships as $sponsor) {
        $count = 0;
        
        if (!empty($sponsor->Sponsorship_Name) && is_numeric($sponsor->Sponsorship_Name)) {
            $count = (int)$sponsor->Sponsorship_Name;
            echo "    - From Sponsorship_Name: $count\n";
        } elseif ($sponsor->Amount && $sponsor->Amount > 0) {
            $count = (int)$sponsor->Amount;
            echo "    - From Amount: $count\n";
        }
        
        $totalAlumni += $count;
    }
    
    echo "    Total alumni: $totalAlumni\n\n";
}

echo "\n================================================\n";
echo "Expected JSON output:\n";
echo "================================================\n";

// Generate final output
$history = $events->map(function($item) {
    $alumniCount = DB::table('trans_input_data_sponsorship')
        ->where('Input_Data_Id', $item->Input_Data_Id)
        ->get()
        ->reduce(function($carry, $sponsorship) {
            $count = 0;
            
            if (!empty($sponsorship->Sponsorship_Name) && is_numeric($sponsorship->Sponsorship_Name)) {
                $count = (int)$sponsorship->Sponsorship_Name;
            }
            elseif ($sponsorship->Amount && $sponsorship->Amount > 0) {
                $count = (int)$sponsorship->Amount;
            }
            
            return $carry + $count;
        }, 0);
    
    return [
        'Input_Data_Id' => $item->Input_Data_Id,
        'Promotion_Name' => $item->Promotion_Name,
        'Event_Start_Date' => $item->Event_Start_Date,
        'alumni_count' => $alumniCount
    ];
})->toArray();

echo json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
?>
