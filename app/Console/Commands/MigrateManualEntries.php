<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateManualEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:manual-entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data manual entries dari JSON ke tabel manual_entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data manual entries...');

        $migrationService = new \App\Services\ManualEntryMigrationService();
        $migrationService->migrateJsonDataToManualEntries();

        $this->info('Migrasi selesai!');
    }
}
