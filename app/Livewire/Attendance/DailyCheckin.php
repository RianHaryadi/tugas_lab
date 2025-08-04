<?php

namespace App\Livewire\Attendance;

use Livewire\Component;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\WithFileUploads; // <-- Import the trait for file uploads

class DailyCheckin extends Component
{
    use WithFileUploads; // <-- Use the trait

    public $todayAttendance;
    public $currentTime;

    // New properties to manage UI state and data
    public string $attendanceType = ''; // 'session' or 'task'
    public array $selectedSessions = [];
    public $sessionPhoto; // For the file upload

    protected $rules = [
        'attendanceType' => 'required|in:session,task',
        'selectedSessions' => 'required_if:attendanceType,session|array|min:1',
        'sessionPhoto' => 'required_if:attendanceType,session|image|max:2048', // 2MB Max
    ];

    public function mount()
    {
        $this->loadCurrentTime();
        $this->loadTodayAttendance();
    }

    public function loadCurrentTime()
    {
        $this->currentTime = Carbon::now();
    }

    public function loadTodayAttendance()
    {
        $this->todayAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', Carbon::today())
            ->with('sessionAttendances')
            ->first();
    }

    /**
     * Step 1: User clicks the initial "Check In" button.
     */
    public function checkIn()
    {
        if ($this->todayAttendance) {
            return;
        }

        // Create the main attendance record for the day
        $this->todayAttendance = Attendance::create([
            'user_id' => Auth::id(),
            'attendance_date' => Carbon::today(),
            'checked_in_at' => Carbon::now(),
        ]);

        // No need to create session records yet. User will choose next.
        $this->loadTodayAttendance(); 
    }

    /**
     * Step 2: User confirms their attendance choice (Sessions or Task).
     */
    public function confirmAttendanceChoice()
    {
        $this->validateOnly('attendanceType');
        
        // If user chooses 'task', we create a placeholder session record
        if ($this->attendanceType === 'task') {
            $this->todayAttendance->sessionAttendances()->create([
                'session_name' => 'Tugas di Rumah',
                'session_validated_at' => Carbon::now(), // Auto-validated
            ]);
        }
        
        // The UI will now update to show the next step based on the choice
        $this->loadTodayAttendance();
    }
    
    /**
     * Step 3: User submits the selected sessions and photo proof.
     */
    public function submitSessions()
{
    // Jalankan validasi untuk memastikan semua data ada
    $this->validate([
        'selectedSessions' => 'required|array|min:1',
        'sessionPhoto' => 'required|image|max:2048', // Maksimal 2MB
    ]);

    // Pindahkan foto dari folder sementara ke folder 'public/session-proofs'
    $photoPath = $this->sessionPhoto->store('session-proofs', 'public');

    // Simpan data ke database untuk setiap sesi yang dipilih
    foreach ($this->selectedSessions as $sessionNumber) {
        $this->todayAttendance->sessionAttendances()->create([
            'session_name' => 'Sesi ' . $sessionNumber,
            'proof_photo_path' => $photoPath, // Simpan path foto
            'session_validated_at' => now(), // Otomatis validasi
        ]);
    }

    // Kosongkan kembali form dan muat ulang data untuk memperbarui UI
    $this->reset(['selectedSessions', 'sessionPhoto', 'attendanceType']);
    $this->loadTodayAttendance();
}

    /**
     * Final Step: User checks out for the day.
     */
    public function checkOut()
    {
        if (!$this->todayAttendance || $this->todayAttendance->checked_out_at) {
            return;
        }

        $this->todayAttendance->update([
            'checked_out_at' => Carbon::now(),
        ]);

        $this->loadTodayAttendance();
    }

    public function render()
    {
        return view('livewire.attendance.daily-checkin');
    }
}
