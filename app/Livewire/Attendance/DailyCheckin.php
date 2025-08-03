<?php

namespace App\Livewire\Attendance;

use Livewire\Component;

class DailyCheckin extends Component
{
    public string $testMessage = 'Halo, saya dari file PHP!';

    public function render()
    {
        return view('livewire.attendance.daily-checkin');
    }
}