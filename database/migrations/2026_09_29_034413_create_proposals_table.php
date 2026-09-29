<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menyimpan rencana proposal proyek kelompok AI milik mahasiswa.
     */
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();

            // Pemilik proposal. Ikut terhapus otomatis saat user dihapus.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            // Nilai akhir (0-100) diisi dosen/asdos saat peninjauan.
            $table->integer('nilai')->nullable();

            // Catatan revisi/feedback dari dosen/asdos.
            $table->text('catatan_revisi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
