<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'description',
        'status',
        'priority',
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
     * Relasi ke model Project.
     * Satu tugas dimiliki oleh satu proyek.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi ke model User.
     * Satu tugas dikerjakan oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}