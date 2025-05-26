<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event_pengajuan', 'event_id')->onDelete('cascade');
            $table->string('photo_path');
            $table->string('photo_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_photos');
    }
}; 