<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskProgress extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     * Perlu didefinisikan karena nama tabelnya tidak jamak (bukan 'task_progresses').
     *
     * @var string
     */
    protected $table = 'task_progress';

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'status_before',
        'status_after',
        'notes',
    ];

    /**
     * Relasi ke model Task.
     * Satu log progres dimiliki oleh satu tugas.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relasi ke model User.
     * Satu log progres dibuat oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}