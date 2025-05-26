<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasi_event', function (Blueprint $table) {
            $table->id('verifikasi_id');
            $table->foreignId('event_id')->constrained('event_pengajuan', 'event_id')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('tanggal_verifikasi');
            $table->text('catatan_admin');
            $table->enum('status', ['closed', 'unclosed'])->default('unclosed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_event');
    }
}; 