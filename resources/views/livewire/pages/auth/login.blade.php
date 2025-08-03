<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full">
    {{-- Dependencies for this custom view. Ideally, these should be moved to the main layout file (layouts/guest.blade.php) --}}
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
        .social-btn:hover {
            transform: translateY(-2px);
        }
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>

    <!-- This div is the new page wrapper. It creates the background and centers the form. -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">

        <div class="relative z-10 w-full max-w-md">
            <!-- Floating decoration elements -->
            <div class="absolute -top-16 -left-16 w-32 h-32 rounded-full bg-indigo-200 opacity-30 floating"></div>
            <div class="absolute -bottom-16 -right-16 w-32 h-32 rounded-full bg-blue-200 opacity-30 floating" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/4 -right-20 w-24 h-24 rounded-full bg-purple-200 opacity-30 floating" style="animation-delay: 4s;"></div>
            
            <!-- I removed dark:bg-gray-800 to keep the form white even in dark mode -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <!-- Decorative header -->
                <div class="bg-gradient-to-r from-indigo-500 to-blue-600 p-6 text-center relative">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500"></div>
                    <div class="w-20 h-20 bg-white rounded-full shadow-md mx-auto flex items-center justify-center mb-4">
                        <i class="fas fa-user-lock text-indigo-600 text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Welcome Back</h1>
                    <p class="text-indigo-100 mt-1">Sign in to continue your journey</p>
                </div>
                
                <!-- Login form -->
                <div class="p-8">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form wire:submit="login" class="space-y-5">
                        <!-- Email field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="input-field relative rounded-lg border border-gray-300 transition-all">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input wire:model="form.email" type="email" id="email" name="email" required
                                       class="block w-full pl-10 pr-3 py-3 border-none bg-transparent focus:ring-0 focus:outline-none"
                                       placeholder="your@email.com">
                            </div>
                            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                        </div>
                        
                        <!-- Password field -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                 @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" wire:navigate class="text-xs text-indigo-600 hover:text-indigo-500 transition-colors">Forgot password?</a>
                                @endif
                            </div>
                            <div class="input-field relative rounded-lg border border-gray-300 transition-all">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input wire:model="form.password" type="password" id="password" name="password" required
                                       class="block w-full pl-10 pr-10 py-3 border-none bg-transparent focus:ring-0 focus:outline-none"
                                       placeholder="••••••••">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i id="togglePasswordIcon" class="fas fa-eye text-gray-400 hover:text-gray-500"></i>
                                </button>
                            </div>
                             <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                        </div>
                        
                        <!-- Remember me -->
                        <div class="flex items-center">
                            <input wire:model="form.remember" type="checkbox" id="remember" name="remember"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        
                        <!-- Submit button -->
                        <button type="submit"
                                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            <span wire:loading.remove wire:target="login">Sign In</span>
                            <span wire:loading wire:target="login">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </form>
                    
                    <!-- Divider -->
                    <div class="mt-6 relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-2 bg-white text-sm text-gray-500">Or continue with</span>
                        </div>
                    </div>
                <!-- Footer -->
                 @if (Route::has('register'))
                    <div class="bg-gray-50 px-6 py-4 text-center">
                        <p class="text-xs text-gray-600">
                            Don't have an account? 
                            <a href="{{ route('register') }}" wire:navigate class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">Sign up</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Change icon
                    toggleIcon.classList.toggle('fa-eye');
                    toggleIcon.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</div>
