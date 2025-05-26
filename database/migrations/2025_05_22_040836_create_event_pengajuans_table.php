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
        Schema::create('event_pengajuan', function (Blueprint $table) {
            $table->id('event_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('judul_event');
            $table->string('jenis_kegiatan');
            $table->string('total_pembiayaan');
            $table->string('proposal');
            $table->text('deskripsi');
            $table->date('tanggal_pengajuan')->default(now());
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_pengajuan');
    }
};
