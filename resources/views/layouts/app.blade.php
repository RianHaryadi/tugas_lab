<!DOCTYPE html>
<html>
<head>
    <title>Daily Attendance</title>
    @livewireStyles
</head>
<body>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daily Attendance') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <livewire:attendance.daily-checkin />
            </div>
        </div>
    </x-app-layout>
    @livewireScripts
</body>
</html>