<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full">
    {{-- Dependencies for this custom view --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        .input-field:focus-within {
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
        }
    </style>

    <!-- Page wrapper -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">

        <div class="relative z-10 w-full max-w-md">
            <!-- Floating decoration elements -->
            <div class="absolute -top-16 -left-16 w-32 h-32 rounded-full bg-indigo-200 opacity-30 floating"></div>
            <div class="absolute -bottom-16 -right-16 w-32 h-32 rounded-full bg-blue-200 opacity-30 floating" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/4 -right-20 w-24 h-24 rounded-full bg-purple-200 opacity-30 floating" style="animation-delay: 4s;"></div>
            
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <!-- Decorative header -->
                <div class="bg-gradient-to-r from-indigo-500 to-blue-600 p-6 text-center relative">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500"></div>
                    <div class="w-20 h-20 bg-white rounded-full shadow-md mx-auto flex items-center justify-center mb-4">
                        <i class="fas fa-user-plus text-indigo-600 text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Create Account</h1>
                    <p class="text-indigo-100 mt-1">Join us and start your journey</p>
                </div>
                
                <!-- Form area -->
                <div class="p-8">
                    <form wire:submit="register" class="space-y-5">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <div class="input-field relative rounded-lg border border-gray-300 transition-all">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input wire:model="name" type="text" id="name" name="name" required autofocus autocomplete="name"
                                       class="block w-full pl-10 pr-3 py-3 border-none bg-transparent focus:ring-0 focus:outline-none"
                                       placeholder="John Doe">
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="input-field relative rounded-lg border border-gray-300 transition-all">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input wire:model="email" type="email" id="email" name="email" required autocomplete="username"
                                       class="block w-full pl-10 pr-3 py-3 border-none bg-transparent focus:ring-0 focus:outline-none"
                                       placeholder="your@email.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="input-field relative rounded-lg border border-gray-300 transition-all">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input wire:model="password" type="password" id="password" name="password" required autocomplete="new-password"
                                       class="block w-full pl-10 pr-3 py-3 border-none bg-transparent focus:ring-0 focus:outline-none"
                                       placeholder="••••••••">
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <div class="input-field relative rounded-lg border border-gray-300 transition-all">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input wire:model="password_confirmation" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                       class="block w-full pl-10 pr-3 py-3 border-none bg-transparent focus:ring-0 focus:outline-none"
                                       placeholder="••••••••">
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                        
                        <!-- Submit button -->
                        <button type="submit"
                                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            <span wire:loading.remove wire:target="register">
                                {{ __('Register') }}
                            </span>
                            <span wire:loading wire:target="register">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 text-center">
                    <p class="text-xs text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" wire:navigate class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
