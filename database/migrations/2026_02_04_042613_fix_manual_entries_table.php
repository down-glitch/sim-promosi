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
        // Hapus tabel manual_entries jika sudah ada
        Schema::dropIfExists('manual_entries');

        // Buat ulang tabel dengan tipe data yang sesuai
        Schema::create('manual_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('input_data_id')->nullable(); // Harus sesuai dengan tipe kolom Input_Data_Id di trans_input_data
            $table->string('province')->nullable(); // Provinsi
            $table->string('city')->nullable(); // Kabupaten/Kota
            $table->string('school_name')->nullable(); // Nama sekolah
            $table->string('school_address')->nullable(); // Alamat sekolah
            $table->string('contact_person')->nullable(); // Kontak person
            $table->string('phone_number')->nullable(); // Nomor telepon
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps(); // created_at dan updated_at

            // Foreign key constraint - tanpa definisi constraint langsung di kolom untuk menghindari masalah kompatibilitas
            $table->foreign('input_data_id')->references('Input_Data_Id')->on('trans_input_data')->onDelete('cascade');

            // Index untuk kolom yang sering digunakan dalam query
            $table->index(['province', 'city']);
            $table->index('input_data_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_entries');
    }
};
