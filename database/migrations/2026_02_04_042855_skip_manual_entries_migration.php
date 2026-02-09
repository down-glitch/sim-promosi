<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tandai migrasi bermasalah sebagai sudah dijalankan
        DB::table('migrations')->insert([
            'migration' => '2026_02_04_041633_create_manual_entries_table',
            'batch' => 2
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus entri migrasi
        DB::table('migrations')
            ->where('migration', '2026_02_04_041633_create_manual_entries_table')
            ->delete();
    }
};
