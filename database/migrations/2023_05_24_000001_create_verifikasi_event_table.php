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
        Schema::create('verifikasi_event', function (Blueprint $table) {
            $table->id('verifikasi_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('tanggal_verifikasi', 100)->nullable();
            $table->text('catatan_admin')->nullable();
            $table->string('status', 50);
            $table->timestamps();

            $table->foreign('event_id')->references('event_id')->on('event_pengajuan')->onDelete('cascade');
            $table->foreign('admin_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_event');
    }
}; 