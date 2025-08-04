@php
    use Carbon\Carbon;
@endphp

<div class="max-w-6xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-1">Manajemen Jadwal Tugas 🗓️</h1>
                <p class="text-blue-100 text-lg">Kelola jadwal Anda, laporkan ketidakhadiran, dan bantu rekan</p>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mx-6 mt-6 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mx-6 mt-6 p-4 bg-rose-100 border-l-4 border-rose-500 text-rose-700 rounded-lg shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="p-8 border-b border-gray-200">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-800">Jadwal Saya</h2>
                <div class="text-sm text-gray-500">
                    Hari Ini: {{ Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($schedules as $schedule)
                    <div class="border border-gray-200 rounded-lg p-5 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
                        <h3 class="font-bold text-xl text-gray-900 mb-1">{{ $schedule['role'] }}</h3>
                        <div class="text-sm text-gray-600 mb-2">
                            Tanggal: {{ Carbon::parse($schedule['date'])->translatedFormat('l, d F Y') }}
                        </div>
                        <p class="text-gray-700 mb-2">{{ $schedule['description'] }}</p>

                        @if ($schedule['is_sick_leave'])
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Perlu Backup (Sakit)</span>
                        @elseif ($schedule['backup_person_id'])
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Di-backup oleh: {{ $schedule['backup_person']['name'] ?? 'N/A' }}
                            </span>
                        @endif

                        <div class="flex justify-end space-x-2 mt-4">
                            @if (!$schedule['is_sick_leave'])
                                <button wire:click="confirmSickLeave({{ $schedule['id'] }})" class="text-rose-600 hover:text-rose-800 text-sm font-medium">Laporkan Sakit</button>
                            @endif
                            <button wire:click="editSchedule({{ $schedule['id'] }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                            <button wire:click="deleteSchedule({{ $schedule['id'] }})" class="text-gray-500 hover:text-gray-700 text-sm font-medium">Hapus</button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-10 border border-gray-200 rounded-lg bg-gray-50">
                        <p class="text-lg mb-2">Tidak ada jadwal tugas saat ini. 😔</p>
                        <p class="text-md">Klik "Tambah Jadwal Baru" untuk memulai!</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if (count($availableSickLeaves) > 0)
            <div class="p-8 bg-gray-50 border-t border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Jadwal Sakit yang Tersedia untuk Backup 🚑</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($availableSickLeaves as $sickSchedule)
                        <div class="border border-rose-300 rounded-lg p-5 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
                            <h3 class="font-bold text-xl text-rose-800 mb-1">{{ $sickSchedule['role'] }}</h3>
                            <div class="text-sm text-gray-600 mb-2">
                                Tanggal: {{ Carbon::parse($sickSchedule['date'])->translatedFormat('l, d F Y') }}
                            </div>
                            <p class="text-gray-700 mb-2">Dari: {{ $sickSchedule['user']['name'] ?? 'N/A' }}</p>
                            <p class="text-gray-700 mb-2">{{ $sickSchedule['description'] }}</p>

                            <div class="flex justify-end mt-4">
                                <button wire:click="takeBackup({{ $sickSchedule['id'] }})" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Ambil Alih</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Sakit --}}
    @if ($confirmingSickLeave)
        <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Konfirmasi Laporan Sakit 🤒</h3>
                <p class="text-base text-gray-700 mb-6">
                    Apakah Anda yakin ingin menandai jadwal ini sebagai sakit?
                </p>
                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="$set('confirmingSickLeave', null)"
                            class="px-5 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="button" wire:click="markAsSickLeave"
                            class="px-5 py-2 bg-rose-600 text-white rounded-lg shadow-md hover:bg-rose-700">
                        Ya, Laporkan Sakit
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
