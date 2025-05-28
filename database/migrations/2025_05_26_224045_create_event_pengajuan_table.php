<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('event_pengajuan', function (Blueprint $table) {
            $table->id('event_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->string('judul_event');
            $table->string('jenis_kegiatan');
            $table->string('total_pembiayaan');
            $table->string('proposal');
            $table->text('deskripsi');
            $table->date('tanggal_pengajuan');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_pengajuan');
    }
};