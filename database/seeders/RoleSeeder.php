<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat peran hanya jika peran tersebut belum ada
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Programmer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Asisten Lab', 'guard_name' => 'web']);
    }
}