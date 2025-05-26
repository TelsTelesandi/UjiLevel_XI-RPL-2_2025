<?php

use App\Models\User;
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
            $table->foreignId('event_id')->constrained('event_pengajuan', 'event_id')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->enum('status', ['closed', 'unclosed'])->default('unclosed');
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
