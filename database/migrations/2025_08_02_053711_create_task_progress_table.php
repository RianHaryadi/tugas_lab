<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade'); // Relasi ke tabel tasks
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke user yang update
            $table->string('status_before');
            $table->string('status_after');
            $table->text('notes')->nullable(); // Catatan saat update progress
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_progress');
    }
};