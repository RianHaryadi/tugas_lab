<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // Pastikan ini di-import

class MakeFilamentUserWithRole extends Command
{
    protected $signature = 'filament:user-with-role';
    protected $description = 'Create a new Filament user with a selected role';

    public function handle()
    {
        $name = $this->ask('What is the user\'s name?');
        $email = $this->ask('What is the user\'s email?');
        $password = $this->secret('What is the password?');

        // Tampilkan pilihan role
        $roles = ['programmer', 'assistant'];
        $selectedRole = $this->choice('Select a role for the user', $roles, 0);

        // Validasi input
        if (empty($name) || empty($email) || empty($password)) {
            $this->error('Name, email, and password are required.');
            return 1;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email format.');
            return 1;
        }
        if (User::where('email', $email)->exists()) {
            $this->error('Email already exists.');
            return 1;
        }

        // --- PERUBAHAN UTAMA DI SINI ---
        // Buat peran jika belum ada di database
        Role::firstOrCreate(['name' => $selectedRole, 'guard_name' => 'web']);
        // ---------------------------------

        // Buat user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            // Tetapkan role
            $user->assignRole($selectedRole);

            $this->info("User {$name} created successfully with role {$selectedRole}.");
        } catch (\Exception $e) {
            $this->error('Failed to create user: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}