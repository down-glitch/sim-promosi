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
        // Hapus duplikat dari tabel mstr_schools sebelum menambahkan constraint unik
        $duplicateRecords = \DB::select("
            SELECT NAME, PROVINCE, CITY, COUNT(*) as count
            FROM mstr_schools
            GROUP BY NAME, PROVINCE, CITY
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicateRecords as $record) {
            // Ambil semua ID duplikat kecuali satu
            $duplicateIds = \DB::select("
                SELECT INSTITUTION_CODE
                FROM mstr_schools
                WHERE NAME = ? AND PROVINCE = ? AND CITY = ?
                ORDER BY INSTITUTION_CODE
            ", [$record->NAME, $record->PROVINCE, $record->CITY]);

            // Hapus semua kecuali yang pertama
            for ($i = 1; $i < count($duplicateIds); $i++) {
                \DB::delete("
                    DELETE FROM mstr_schools
                    WHERE INSTITUTION_CODE = ?
                ", [$duplicateIds[$i]->INSTITUTION_CODE]);
            }
        }

        // Tambahkan constraint unik pada tabel mstr_schools untuk mencegah duplikasi
        try {
            Schema::table('mstr_schools', function (Blueprint $table) {
                $table->unique(['NAME', 'PROVINCE', 'CITY'], 'unique_school_location');
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, lewati
        }

        // Tambahkan constraint pada tabel trans_input_data untuk memastikan data valid
        try {
            Schema::table('trans_input_data', function (Blueprint $table) {
                $table->foreign('Input_Letter_Request_Id')->references('Input_Letter_Request_Id')->on('trans_letter_request')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, lewati
        }

        // Tambahkan constraint pada tabel trans_input_data_schools_id
        try {
            Schema::table('trans_input_data_schools_id', function (Blueprint $table) {
                $table->foreign('School_Id')->references('INSTITUTION_CODE')->on('mstr_schools')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, lewati
        }

        // Tambahkan constraint pada tabel trans_input_data_person
        try {
            Schema::table('trans_input_data_person', function (Blueprint $table) {
                $table->foreign('Input_Data_Id')->references('Input_Data_Id')->on('trans_input_data')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, lewati
        }

        // Tambahkan constraint pada tabel trans_input_data_department
        try {
            Schema::table('trans_input_data_department', function (Blueprint $table) {
                $table->foreign('Input_Data_Id')->references('Input_Data_Id')->on('trans_input_data')->onDelete('cascade');
                $table->foreign('Department_Id')->references('DEPARTMENT_ID')->on('mstr_department')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, lewati
        }

        // Tambahkan constraint pada tabel trans_input_data_sponsorship
        try {
            Schema::table('trans_input_data_sponsorship', function (Blueprint $table) {
                $table->foreign('Input_Data_Id')->references('Input_Data_Id')->on('trans_input_data')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, lewati
        }

        // Tambahkan constraint pada tabel manual_entries
        try {
            Schema::table('manual_entries', function (Blueprint $table) {
                $table->foreign('input_data_id')->references('Input_Data_Id')->on('trans_input_data')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, lewati
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus constraint unik
        Schema::table('mstr_schools', function (Blueprint $table) {
            $table->dropUnique(['unique_school_location']);
        });

        // Hapus foreign key constraints
        Schema::table('trans_input_data', function (Blueprint $table) {
            $table->dropForeign(['Input_Letter_Request_Id']);
        });

        Schema::table('trans_input_data_schools_id', function (Blueprint $table) {
            $table->dropForeign(['School_Id']);
        });

        Schema::table('trans_input_data_person', function (Blueprint $table) {
            $table->dropForeign(['Input_Data_Id']);
        });

        Schema::table('trans_input_data_department', function (Blueprint $table) {
            $table->dropForeign(['Input_Data_Id']);
            $table->dropForeign(['Department_Id']);
        });

        Schema::table('trans_input_data_sponsorship', function (Blueprint $table) {
            $table->dropForeign(['Input_Data_Id']);
        });

        Schema::table('manual_entries', function (Blueprint $table) {
            $table->dropForeign(['input_data_id']);
        });
    }
};
