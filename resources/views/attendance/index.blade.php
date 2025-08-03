<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daily Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Ini akan memanggil komponen absensi yang telah kita buat --}}
            <livewire:attendance.daily-checkin />
        </div>
    </div>
</x-app-layout>