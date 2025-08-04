<div 
    x-data="{ 
        currentTime: '{{ $currentTime->format('H:i:s') }}',
        init() {
            setInterval(() => {
                let d = new Date();
                this.currentTime = d.toLocaleTimeString('id-ID');
            }, 1000);
        }
    }"
    class="bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-xl p-6 md:p-8 max-w-2xl mx-auto border border-gray-200"
>
    {{-- Header: Judul dan Waktu --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-4 border-b border-gray-200">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kehadiran Harian</h2>
            <p class="text-sm text-gray-500">{{ $currentTime->format('l, j F Y') }}</p>
        </div>
        <div class="mt-2 md:mt-0">
            <div class="text-3xl font-mono font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent" x-text="currentTime"></div>
        </div>
    </div>

    {{-- Konten Utama --}}
    <div class="mt-6">

        {{-- =============================================================== --}}
        {{-- Tampilan 1: Sebelum ABSEN --}}
        {{-- =============================================================== --}}
        @if (!$todayAttendance)
            <div class="text-center py-10">
                <div class="mx-auto w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-600 mb-6">Anda belum ABSEN hari ini.</p>
                <button wire:click="checkIn" wire:loading.attr="disabled" class="w-full md:w-auto text-center bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-4 px-8 rounded-xl transition duration-300 ease-in-out transform hover:scale-[1.02] shadow-md disabled:opacity-50">
                    <span wire:loading.remove wire:target="checkIn">ABSEN</span>
                    <span wire:loading wire:target="checkIn">Memproses...</span>
                </button>
            </div>
        
        {{-- =============================================================== --}}
        {{-- Tampilan 2: Setelah ABSEN, Sebelum Memilih Aktivitas --}}
        {{-- =============================================================== --}}
        @elseif ($todayAttendance && $todayAttendance->sessionAttendances->isEmpty())
            <div class="space-y-6">
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-center shadow-sm">
                    <div class="flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="font-bold text-emerald-800">Berhasil ABSEN pada pukul {{ \Carbon\Carbon::parse($todayAttendance->checked_in_at)->format('H:i') }}</p>
                    </div>
                    <p class="text-sm text-emerald-700 mt-1">Silakan pilih aktivitas Anda untuk hari ini.</p>
                </div>

                {{-- Pemilihan Aktivitas --}}
                <div class="space-y-3">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:border-blue-400 hover:shadow-sm" :class="{'border-blue-500 bg-blue-50 shadow-sm': $wire.attendanceType === 'session'}">
                        <input type="radio" wire:model.live="attendanceType" value="session" class="h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-4 text-gray-800 font-medium">Mengikuti Sesi Laboratorium</span>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:border-blue-400 hover:shadow-sm" :class="{'border-blue-500 bg-blue-50 shadow-sm': $wire.attendanceType === 'task'}">
                        <input type="radio" wire:model.live="attendanceType" value="task" class="h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-4 text-gray-800 font-medium">Mengerjakan Tugas di Rumah</span>
                    </label>
                    @error('attendanceType') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                {{-- Formulir untuk Sesi Laboratorium --}}
                <div x-show="$wire.attendanceType === 'session'" x-transition class="space-y-4 p-4 border-l-4 border-blue-300 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-gray-700">Pilih Sesi yang Diikuti:</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach(range(1, 5) as $sessionNumber)
                            <label class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" wire:model="selectedSessions" value="{{ $sessionNumber }}" class="h-4 w-4 rounded text-blue-600 focus:ring-blue-500">
                                <span class="ml-3 text-sm font-medium">Sesi {{ $sessionNumber }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedSessions') <span class="block text-sm text-red-500 mt-2">{{ $message }}</span> @enderror

                    <div class="mt-4">
                        <label for="sessionPhoto" class="block text-sm font-medium text-gray-700">Unggah Bukti Kehadiran:</label>
                        <div class="mt-2 flex items-center">
                            <label for="sessionPhoto" class="flex-1 cursor-pointer">
                                <div class="flex items-center justify-between px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <span class="text-sm text-gray-500 truncate" x-text="sessionPhoto ? sessionPhoto.name : 'Pilih file...'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <input type="file" wire:model="sessionPhoto" id="sessionPhoto" accept="image/*" capture="environment" class="hidden">
                            </label>
                        </div>
                        <div wire:loading wire:target="sessionPhoto" class="text-sm text-gray-500 mt-1 flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengunggah...
                        </div>
                        @error('sessionPhoto') <span class="block text-sm text-red-500 mt-2">{{ $message }}</span> @enderror
                    </div>
                    
                    <button wire:click="submitSessions" wire:loading.attr="disabled" class="w-full mt-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition shadow-md disabled:opacity-50">
                        KIRIM SESI
                    </button>
                </div>

                {{-- Tombol untuk Tugas Rumah --}}
                <div x-show="$wire.attendanceType === 'task'" x-transition>
                    <button wire:click="confirmAttendanceChoice" wire:loading.attr="disabled" class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3 px-4 rounded-lg transition shadow-md disabled:opacity-50">
                        KONFIRMASI TUGAS RUMAH
                    </button>
                </div>
            </div>

        {{-- =============================================================== --}}
        {{-- Tampilan 3: Setelah Aktivitas Dipilih (Menunggu Check Out) --}}
        {{-- =============================================================== --}}
        @else
            <div class="space-y-6">
                {{-- Waktu ABSEN & Check Out --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center">
                    <div class="bg-gradient-to-br from-emerald-50 to-white p-4 rounded-lg border border-emerald-200 shadow-sm">
                        <p class="text-sm font-medium text-emerald-700">ABSEN</p>
                        <p class="text-2xl font-bold text-emerald-800">{{ \Carbon\Carbon::parse($todayAttendance->checked_in_at)->format('H:i:s') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-lg border {{ $todayAttendance->checked_out_at ? 'border-red-200' : 'border-gray-200' }} shadow-sm">
                        <p class="text-sm font-medium {{ $todayAttendance->checked_out_at ? 'text-red-700' : 'text-gray-500' }}">
                            {{ $todayAttendance->checked_out_at ? 'Check Out' : 'Belum Check Out' }}
                        </p>
                        <p class="text-2xl font-bold {{ $todayAttendance->checked_out_at ? 'text-red-800' : 'text-gray-400' }}">
                            {{ $todayAttendance->checked_out_at ? \Carbon\Carbon::parse($todayAttendance->checked_out_at)->format('H:i:s') : '--:--:--' }}
                        </p>
                    </div>
                </div>

                {{-- Aktivitas yang Tercatat --}}
                <div>
                    <h3 class="font-semibold text-gray-700 mb-3">Aktivitas Hari Ini</h3>
                    <ul class="space-y-3">
                        @foreach ($todayAttendance->sessionAttendances as $session)
                            <li class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition">
                                <span class="font-medium text-gray-800">{{ $session->session_name }}</span>
                                @if ($session->session_validated_at)
                                    <span class="flex items-center text-sm font-semibold text-emerald-600 bg-emerald-100 px-3 py-1 rounded-full">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Tervalidasi
                                    </span>
                                @else
                                    <span class="flex items-center text-sm font-semibold text-amber-600 bg-amber-100 px-3 py-1 rounded-full">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Menunggu
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Tombol Aksi Check Out --}}
                @if (!$todayAttendance->checked_out_at)
                    <button wire:click="checkOut" wire:loading.attr="disabled" class="w-full text-center bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-bold py-4 px-4 rounded-lg transition duration-300 ease-in-out shadow-md disabled:opacity-50">
                        <span wire:loading.remove wire:target="checkOut">CHECK OUT</span>
                        <span wire:loading wire:target="checkOut">Memproses...</span>
                    </button>
                @else
                    <div class="text-center bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                        <p class="font-medium text-gray-700">Anda telah menyelesaikan kehadiran hari ini.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>