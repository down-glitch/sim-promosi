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
        // Tambahkan indeks pada tabel mstr_schools jika belum ada
        $indexesToCreate = [
            ['table' => 'mstr_schools', 'columns' => ['PROVINCE', 'CITY'], 'name' => 'idx_province_city'],
            ['table' => 'mstr_schools', 'columns' => 'INSTITUTION_CODE', 'name' => 'idx_institution_code'],
            ['table' => 'trans_input_data', 'columns' => 'Input_Data_Type', 'name' => 'idx_input_data_type'],
            ['table' => 'trans_input_data', 'columns' => 'Event_Start_Date', 'name' => 'idx_event_start_date'],
            ['table' => 'trans_input_data', 'columns' => ['Input_Data_Type', 'Event_Start_Date'], 'name' => 'idx_type_date'],
            ['table' => 'trans_input_data_schools_id', 'columns' => 'Input_Data_Id', 'name' => 'idx_input_data_id'],
            ['table' => 'trans_input_data_schools_id', 'columns' => 'School_Id', 'name' => 'idx_school_id'],
            ['table' => 'trans_input_data_schools_id', 'columns' => ['Input_Data_Id', 'School_Id'], 'name' => 'idx_input_school'],
            ['table' => 'trans_input_data_person', 'columns' => 'Input_Data_Id', 'name' => 'idx_person_input_data_id'],
            ['table' => 'trans_input_data_department', 'columns' => 'Input_Data_Id', 'name' => 'idx_dept_input_data_id'],
            ['table' => 'trans_input_data_department', 'columns' => 'Department_Id', 'name' => 'idx_dept_id'],
            ['table' => 'trans_input_data_sponsorship', 'columns' => 'Input_Data_Id', 'name' => 'idx_sponsorship_input_data_id'],
        ];

        foreach ($indexesToCreate as $indexInfo) {
            $tableName = $indexInfo['table'];
            $columns = $indexInfo['columns'];
            $indexName = $indexInfo['name'];

            // Cek apakah indeks sudah ada
            $existingIndexes = \DB::select("
                SELECT INDEX_NAME
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND INDEX_NAME = ?
            ", [$tableName, $indexName]);

            if (empty($existingIndexes)) {
                Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus indeks pada tabel mstr_schools
        Schema::table('mstr_schools', function (Blueprint $table) {
            $table->dropIndex(['idx_province_city']);
            $table->dropIndex(['idx_institution_code']);
        });

        // Hapus indeks pada tabel trans_input_data
        Schema::table('trans_input_data', function (Blueprint $table) {
            $table->dropIndex(['idx_input_data_type']);
            $table->dropIndex(['idx_event_start_date']);
            $table->dropIndex(['idx_type_date']);
        });

        // Hapus indeks pada tabel trans_input_data_schools_id
        Schema::table('trans_input_data_schools_id', function (Blueprint $table) {
            $table->dropIndex(['idx_input_data_id']);
            $table->dropIndex(['idx_school_id']);
            $table->dropIndex(['idx_input_school']);
        });

        // Hapus indeks pada tabel trans_input_data_person
        Schema::table('trans_input_data_person', function (Blueprint $table) {
            $table->dropIndex(['idx_person_input_data_id']);
        });

        // Hapus indeks pada tabel trans_input_data_department
        Schema::table('trans_input_data_department', function (Blueprint $table) {
            $table->dropIndex(['idx_dept_input_data_id']);
            $table->dropIndex(['idx_dept_id']);
        });

        // Hapus indeks pada tabel trans_input_data_sponsorship
        Schema::table('trans_input_data_sponsorship', function (Blueprint $table) {
            $table->dropIndex(['idx_sponsorship_input_data_id']);
        });
    }
};
