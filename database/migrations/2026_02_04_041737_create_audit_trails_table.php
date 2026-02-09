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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // Nama tabel yang diubah
            $table->unsignedBigInteger('record_id'); // ID record yang diubah
            $table->string('action'); // Jenis aksi: INSERT, UPDATE, DELETE
            $table->json('old_values')->nullable(); // Nilai lama sebelum perubahan
            $table->json('new_values')->nullable(); // Nilai baru setelah perubahan
            $table->string('user_id')->nullable(); // ID user yang melakukan perubahan
            $table->string('ip_address')->nullable(); // IP address user
            $table->text('user_agent')->nullable(); // User agent browser
            $table->timestamp('created_at')->useCurrent();

            // Index untuk kolom yang sering digunakan dalam query
            $table->index(['table_name', 'record_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
