<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'deadline',
    ];

    /**
     * Tipe data yang harus di-casting (diubah).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    /**
     * Relasi ke model Task.
     * Satu proyek bisa memiliki banyak tugas.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}