<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CalendarEvent extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'color',
        'is_all_day',
    ];

    /**
     * Tipe data yang harus di-casting (diubah).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_all_day' => 'boolean',
    ];

    /**
     * Relasi polimorfik opsional.
     * Ini memungkinkan sebuah event untuk terhubung ke model lain,
     * seperti Project atau Task, jika diperlukan di masa depan.
     */
    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }
}