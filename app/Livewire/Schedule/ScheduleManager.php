<?php

namespace App\Livewire\Schedule;

use Livewire\Component;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleManager extends Component
{
    public $schedules = [];
    public $availableSickLeaves = [];

    // Form input
    public $scheduleId = null;
    public $date = '';
    public $role = 'programmer';
    public $description = '';
    public $confirmingSickLeave = null;

    protected function rules()
    {
        return [
            'date' => 'required|date',
            'role' => 'required|in:programmer,asisten',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function mount()
    {
        $this->loadSchedules();
        $this->loadAvailableSickLeavesForBackup();
    }

    public function loadSchedules()
    {
        if (Auth::check()) {
            $this->schedules = Schedule::with(['user', 'backupPerson'])
                ->where('user_id', Auth::id())
                ->orderBy('date')
                ->get()
                ->toArray();
        } else {
            $this->schedules = [];
        }
    }

    public function loadAvailableSickLeavesForBackup()
    {
        $this->availableSickLeaves = Schedule::with('user')
            ->where('is_sick_leave', true)
            ->whereNull('backup_person_id')
            ->where('user_id', '!=', Auth::id())
            ->get()
            ->toArray();
    }

    public function createSchedule()
    {
        $this->resetForm();
        $this->dispatch('open-schedule-modal');
    }

    public function editSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->user_id !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki izin.');
            return;
        }

        $this->scheduleId = $schedule->id;
        $this->date = $schedule->date?->format('Y-m-d');
        $this->role = $schedule->role;
        $this->description = $schedule->description;

        $this->dispatch('open-schedule-modal');
    }

    public function saveSchedule()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'date' => $this->date,
            'role' => $this->role,
            'description' => $this->description,
            'is_sick_leave' => false,
            'backup_person_id' => null,
        ];

        if ($this->scheduleId) {
            Schedule::findOrFail($this->scheduleId)->update($data);
            session()->flash('success', 'Jadwal berhasil diperbarui.');
        } else {
            Schedule::create($data);
            session()->flash('success', 'Jadwal berhasil ditambahkan.');
        }

        $this->resetForm();
        $this->loadSchedules();
        $this->loadAvailableSickLeavesForBackup();
        $this->dispatch('close-schedule-modal');
    }

    public function confirmSickLeave($id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->user_id !== Auth::id()) {
            session()->flash('error', 'Tidak bisa mengubah status sakit.');
            return;
        }
        $this->confirmingSickLeave = $id;
    }

    public function markAsSickLeave()
    {
        if (!$this->confirmingSickLeave) return;

        $schedule = Schedule::findOrFail($this->confirmingSickLeave);
        $schedule->update([
            'is_sick_leave' => true,
            'backup_person_id' => null,
        ]);

        session()->flash('success', 'Ditandai sakit. Menunggu pengganti.');
        $this->confirmingSickLeave = null;
        $this->loadSchedules();
        $this->loadAvailableSickLeavesForBackup();
    }

    public function takeBackup($id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->is_sick_leave && !$schedule->backup_person_id && $schedule->user_id !== Auth::id()) {
            $schedule->update(['backup_person_id' => Auth::id()]);
            session()->flash('success', 'Berhasil mengambil jadwal backup.');
        } else {
            session()->flash('error', 'Tidak dapat mengambil alih.');
        }

        $this->loadSchedules();
        $this->loadAvailableSickLeavesForBackup();
    }

    public function deleteSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        if ($schedule->user_id === Auth::id()) {
            $schedule->delete();
            session()->flash('success', 'Jadwal dihapus.');
        } else {
            session()->flash('error', 'Tidak memiliki izin hapus.');
        }

        $this->loadSchedules();
        $this->loadAvailableSickLeavesForBackup();
    }

    private function resetForm()
    {
        $this->reset(['scheduleId', 'date', 'role', 'description']);
        $this->role = 'programmer';
    }

    public function render()
    {
        return view('livewire.schedule.schedule-manager');
    }
}
