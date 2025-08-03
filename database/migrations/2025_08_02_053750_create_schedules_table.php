<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();

            // --- KOLOM UNTUK JADWAL SEKALI JALAN ---
            // Diisi jika ini adalah jadwal spesifik, misal: "Meeting Project X"
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            // --- KOLOM UNTUK JADWAL BERULANG ---
            // Diisi jika ini adalah jadwal rutin, misal: "Jaga Lab"
            $table->enum('day_of_week', [
                'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
            ])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};