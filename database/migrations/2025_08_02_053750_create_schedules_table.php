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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date')->comment('Tanggal tugas');
            $table->enum('role', ['programmer', 'asisten'])->comment('Peran di hari tersebut');

            $table->boolean('is_sick_leave')->default(false)->comment('Menandakan apakah user izin sakit');
            $table->foreignId('backup_person_id')->nullable()->constrained('users')->onDelete('set null')->comment('Orang pengganti jika sakit');

            $table->text('description')->nullable()->comment('Keterangan tambahan jika ada');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
