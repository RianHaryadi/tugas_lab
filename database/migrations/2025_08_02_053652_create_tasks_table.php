<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // Relasi ke tabel projects
            $table->foreignId('user_id')->constrained()->onDelete('cascade');    // Relasi ke programmer yang ditugaskan
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['Belum Dikerjakan', 'Proses', 'Selesai', 'Revisi'])->default('Belum Dikerjakan');
            $table->integer('priority')->default(1); // 1: Low, 2: Medium, 3: High
            $table->date('start_date');
            $table->date('deadline');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};