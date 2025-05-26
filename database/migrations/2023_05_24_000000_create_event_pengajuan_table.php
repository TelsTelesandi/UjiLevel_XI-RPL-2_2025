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
            $table->unsignedBigInteger('user_id');
            $table->string('judul_event', 200);
            $table->string('jenis_kegiatan', 200);
            $table->string('total_pembiayaan', 100);
            $table->string('proposal', 100);
            $table->text('deskripsi');
            $table->date('tanggal_pengajuan');
            $table->string('status', 50);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
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