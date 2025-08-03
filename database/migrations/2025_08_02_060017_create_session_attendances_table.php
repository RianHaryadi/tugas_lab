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
        Schema::create('session_attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
        $table->enum('session_name', ['Sesi 1', 'Sesi 2', 'Sesi 3', 'Sesi 4', 'Sesi 5']);
        $table->timestamp('session_validated_at'); // Waktu saat sesi divalidasi
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_attendances');
    }
};
