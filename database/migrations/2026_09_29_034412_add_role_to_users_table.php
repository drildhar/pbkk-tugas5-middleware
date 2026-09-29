<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kolom `role` ditambahkan lewat migration terpisah (bukan mengubah
     * migration bawaan) agar riwayat schema tetap jelas dan aman untuk
     * database yang sudah terlanjur dimigrasi.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nilai yang diizinkan: mahasiswa | dosen | asdos.
            // `after()` hanya berefek di MySQL dan diabaikan secara aman di SQLite.
            $table->enum('role', ['mahasiswa', 'dosen', 'asdos'])
                ->default('mahasiswa')
                ->after('password')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
