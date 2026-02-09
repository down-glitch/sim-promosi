<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan kolom untuk menyimpan informasi impor
        Schema::table('trans_input_data', function (Blueprint $table) {
            $table->string('import_batch_id')->nullable()->after('Input_Letter_Request_Id'); // ID batch impor
            $table->timestamp('imported_at')->nullable()->after('import_batch_id'); // Waktu impor
        });

        // Tambahkan indeks untuk kolom impor
        Schema::table('trans_input_data', function (Blueprint $table) {
            $table->index('import_batch_id', 'idx_import_batch_id');
            $table->index('imported_at', 'idx_imported_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trans_input_data', function (Blueprint $table) {
            $table->dropIndex(['idx_import_batch_id']);
            $table->dropIndex(['idx_imported_at']);
            $table->dropColumn(['import_batch_id', 'imported_at']);
        });
    }
};
