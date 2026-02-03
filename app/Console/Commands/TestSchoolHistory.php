<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestSchoolHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:school-history {school_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test getSchoolHistory logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $school_id = $this->argument('school_id');
        
        $this->info("Testing getSchoolHistory for: $school_id");
        $this->line("================================================");
        
        // Get events
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

        $this->line("Found " . count($events) . " events");
        
        foreach ($events as $event) {
            $this->line("  Event: {$event->Promotion_Name} ({$event->Event_Start_Date})");
        }
        
        $this->line("");
        
        // Map with alumni
        $history = $events->map(function($item) {
            $sponsorships = DB::table('trans_input_data_sponsorship')
                ->where('Input_Data_Id', $item->Input_Data_Id)
                ->get();
            
            $alumniCount = 0;
            foreach ($sponsorships as $sponsor) {
                if (!empty($sponsor->Sponsorship_Name) && is_numeric($sponsor->Sponsorship_Name)) {
                    $alumniCount += (int)$sponsor->Sponsorship_Name;
                } elseif ($sponsor->Amount && $sponsor->Amount > 0) {
                    $alumniCount += (int)$sponsor->Amount;
                }
            }
            
            return [
                'Input_Data_Id' => $item->Input_Data_Id,
                'Promotion_Name' => $item->Promotion_Name,
                'Event_Start_Date' => $item->Event_Start_Date,
                'alumni_count' => $alumniCount
            ];
        })->toArray();
        
        $this->line("Final JSON Response:");
        $this->line("================================================");
        $this->line(json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
