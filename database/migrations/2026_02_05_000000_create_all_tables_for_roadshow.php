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
        // Membuat tabel mstr_schools jika belum ada
        if (!Schema::hasTable('mstr_schools')) {
            Schema::create('mstr_schools', function (Blueprint $table) {
                $table->string('INSTITUTION_CODE', 50)->primary();
                $table->string('NAME');
                $table->string('ADDRESS')->nullable();
                $table->string('CITY')->nullable();
                $table->string('PROVINCE')->nullable();
                $table->timestamps();
                
                $table->index(['PROVINCE', 'CITY']);
            });
        }

        // Membuat tabel trans_input_data jika belum ada
        if (!Schema::hasTable('trans_input_data')) {
            Schema::create('trans_input_data', function (Blueprint $table) {
                $table->id('Input_Data_Id');
                $table->integer('Input_Data_Type')->nullable();
                $table->string('Promotion_Name')->nullable();
                $table->date('Event_Start_Date')->nullable();
                $table->date('Event_End_Date')->nullable();
                $table->text('Note')->nullable();
                $table->string('Created_By')->nullable();
                $table->string('Modified_By')->nullable();
                $table->timestamp('Created_Date')->nullable();
                $table->timestamp('Modified_Date')->nullable();
                
                $table->index('Input_Data_Type');
                $table->index('Event_Start_Date');
                $table->index(['Input_Data_Type', 'Event_Start_Date']);
            });
        }

        // Membuat tabel trans_input_data_schools_id jika belum ada
        if (!Schema::hasTable('trans_input_data_schools_id')) {
            Schema::create('trans_input_data_schools_id', function (Blueprint $table) {
                $table->id('Id');
                $table->integer('Input_Data_Id');
                $table->string('School_Id', 50);
                $table->string('Created_By')->nullable();
                $table->string('Modified_By')->nullable();
                $table->timestamp('Created_Date')->nullable();
                $table->timestamp('Modified_Date')->nullable();
                
                $table->index('Input_Data_Id');
                $table->index('School_Id');
            });
        }

        // Membuat tabel trans_input_data_person jika belum ada
        if (!Schema::hasTable('trans_input_data_person')) {
            Schema::create('trans_input_data_person', function (Blueprint $table) {
                $table->id('Id');
                $table->integer('Input_Data_Id');
                $table->string('Name')->nullable();
                $table->string('Created_By')->nullable();
                $table->string('Modified_By')->nullable();
                $table->timestamp('Created_Date')->nullable();
                $table->timestamp('Modified_Date')->nullable();
                
                $table->index('Input_Data_Id');
            });
        }

        // Membuat tabel trans_input_data_department jika belum ada
        if (!Schema::hasTable('trans_input_data_department')) {
            Schema::create('trans_input_data_department', function (Blueprint $table) {
                $table->id('Id');
                $table->integer('Input_Data_Id');
                $table->integer('Department_Id');
                $table->string('Created_By')->nullable();
                $table->string('Modified_By')->nullable();
                $table->timestamp('Created_Date')->nullable();
                $table->timestamp('Modified_Date')->nullable();
                
                $table->index('Input_Data_Id');
                $table->index('Department_Id');
            });
        }

        // Membuat tabel trans_input_data_sponsorship jika belum ada
        if (!Schema::hasTable('trans_input_data_sponsorship')) {
            Schema::create('trans_input_data_sponsorship', function (Blueprint $table) {
                $table->id('Id');
                $table->integer('Input_Data_Id');
                $table->string('Sponsorship_Name')->nullable();
                $table->integer('Amount')->nullable();
                $table->string('Description')->nullable();
                $table->string('Created_By')->nullable();
                $table->string('Modified_By')->nullable();
                $table->timestamp('Created_Date')->nullable();
                $table->timestamp('Modified_Date')->nullable();
                
                $table->index('Input_Data_Id');
            });
        }

        // Membuat tabel manual_entries jika belum ada
        if (!Schema::hasTable('manual_entries')) {
            Schema::create('manual_entries', function (Blueprint $table) {
                $table->id();
                $table->integer('input_data_id')->nullable();
                $table->string('province')->nullable();
                $table->string('city')->nullable();
                $table->string('school_name')->nullable();
                $table->string('school_address')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('phone_number')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['province', 'city']);
                $table->index('input_data_id');
            });
        }
        
        // Menandai bahwa semua migrasi telah dijalankan
        \DB::table('migrations')->insert([
            'migration' => '2026_02_05_000000_create_all_tables_for_roadshow',
            'batch' => 5
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_entries');
        Schema::dropIfExists('trans_input_data_sponsorship');
        Schema::dropIfExists('trans_input_data_department');
        Schema::dropIfExists('trans_input_data_person');
        Schema::dropIfExists('trans_input_data_schools_id');
        Schema::dropIfExists('trans_input_data');
        Schema::dropIfExists('mstr_schools');
        
        \DB::table('migrations')
            ->where('migration', '2026_02_05_000000_create_all_tables_for_roadshow')
            ->delete();
    }
};