<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'role',              // programmer / asisten
        'description',
        'is_sick_leave',
        'backup_person_id',
    ];

    protected $casts = [
        'date' => 'date',               // Cast ke Carbon instance
        'is_sick_leave' => 'boolean',
    ];

    /**
     * Pemilik jadwal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User yang menjadi backup saat user izin sakit.
     */
    public function backupPerson()
    {
        return $this->belongsTo(User::class, 'backup_person_id');
    }

    /**
     * Relasi ke absensi per sesi (jika nanti kamu tambahkan).
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
