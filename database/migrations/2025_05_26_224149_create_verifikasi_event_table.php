<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('verifikasi_event', function (Blueprint $table) {
            $table->id('verifikasi_id');
            $table->foreignId('event_id')->constrained('event_pengajuan', 'event_id');
            $table->foreignId('admin_id')->constrained('users', 'user_id');
            $table->date('tanggal_verifikasi');
            $table->text('catatan_admin')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('verifikasi_event');
    }
};