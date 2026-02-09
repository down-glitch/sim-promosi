<?php

namespace App\Services;

use App\Models\TransInputData;
use App\Models\ManualEntry;
use Illuminate\Support\Facades\DB;

class ManualEntryMigrationService
{
    public function migrateJsonDataToManualEntries()
    {
        // Ambil semua data dari trans_input_data yang memiliki Note (JSON) dan tidak terhubung ke sekolah
        $manualData = DB::table('trans_input_data as td')
            ->leftJoin('trans_input_data_schools_id as tdsi', 'td.Input_Data_Id', '=', 'tdsi.Input_Data_Id')
            ->where('td.Input_Data_Type', 1) // Hanya roadshow
            ->whereNull('tdsi.Input_Data_Id') // Tidak terlink ke sekolah
            ->whereNotNull('td.Note') // Memiliki catatan
            ->select('td.Input_Data_Id', 'td.Note')
            ->get();

        foreach ($manualData as $data) {
            try {
                $note = json_decode($data->Note, true);
                
                if (is_array($note) && isset($note['provinsi']) && isset($note['kabupaten'])) {
                    // Buat entri baru di tabel manual_entries
                    ManualEntry::updateOrCreate(
                        ['input_data_id' => $data->Input_Data_Id], // Cari berdasarkan input_data_id
                        [
                            'province' => $note['provinsi'] ?? null,
                            'city' => $note['kabupaten'] ?? null,
                            'school_name' => $note['sekolah'] ?? null,
                            'notes' => $data->Note // Simpan JSON asli sebagai catatan tambahan
                        ]
                    );
                }
            } catch (\Exception $e) {
                // Log error jika JSON tidak valid
                \Log::warning("Gagal memproses JSON untuk Input_Data_Id: {$data->Input_Data_Id}", [
                    'error' => $e->getMessage(),
                    'json' => $data->Note
                ]);
            }
        }
    }
}