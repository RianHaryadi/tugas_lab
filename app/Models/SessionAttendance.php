<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'session_name',
        'session_validated_at',
    ];

    /**
     * Relasi ke absensi harian.
     * Satu validasi sesi dimiliki oleh satu catatan kehadiran harian.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}