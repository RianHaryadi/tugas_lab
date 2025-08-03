<?php

namespace Database\Seeders; // <-- PASTIKAN NAMESPACE INI BENAR

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder // <-- PASTIKAN NAMA KELAS INI BENAR
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder lain di sini
        $this->call([
            RoleSeeder::class,
        ]);
    }
}