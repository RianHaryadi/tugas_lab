<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    protected $signature = 'make:filament-admin 
                            {--name= : The name of the admin user} 
                            {--email= : The email of the admin user} 
                            {--password= : The password for the admin user} 
                            {--force : Create user even if email does not end with @admin.com}';

    protected $description = 'Create a new Filament admin user with Spatie role "admin"';

    public function handle()
    {
        $this->info('Starting the Filament Admin User creation process...');

        $name = $this->option('name') ?: $this->ask('What is the admin\'s name?');
        $email = $this->option('email') ?: $this->ask('What is the admin\'s email address?');
        $password = $this->option('password') ?: $this->secret('Please provide a secure password (min. 8 characters)');
        $force = $this->option('force');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('User creation failed. Please fix the following errors:');
            foreach ($validator->errors()->all() as $error) {
                $this->line('- ' . $error);
            }
            return 1;
        }

        if (! $force && ! Str::endsWith($email, '@admin.com')) {
            $this->error('Error: Email must end with @admin.com. Use the --force flag to bypass.');
            return 1;
        }

        // Pastikan role 'admin' ada
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $user->assignRole('admin');

            $this->info("✅ Admin user '{$user->name}' created successfully with role 'admin'.");

            $this->table(
                ['Attribute', 'Value'],
                [
                    ['ID', $user->id],
                    ['Name', $user->name],
                    ['Email', $user->email],
                    ['Roles', $user->getRoleNames()->implode(', ')],
                    ['Created At', $user->created_at->toDateTimeString()],
                ]
            );

            return 0;

        } catch (\Exception $e) {
            $this->error('An unexpected error occurred while creating the user:');
            $this->error($e->getMessage());
            return 1;
        }
    }
}
