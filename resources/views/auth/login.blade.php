<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="max-w-4xl w-full mx-auto grid grid-cols-1 md:grid-cols-2 shadow-lg rounded-lg overflow-hidden">
            
            <!-- Left side with background image and text -->
            <div class="hidden md:block bg-cover bg-center" style="background-image: url('https://source.unsplash.com/random/800x600?bus,travel');">
                <div class="flex flex-col justify-center h-full p-8 bg-gray-900 bg-opacity-60">
                    <h2 class="text-4xl font-bold text-white mb-4">Welcome Back</h2>
                    <p class="text-gray-200">Log in to manage your bookings and explore new destinations. Your next adventure is just a click away.</p>
                </div>
            </div>

            <!-- Right side with the form -->
            <div class="p-8 bg-white">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-indigo-600">BooknGo</h1>
                    <p class="text-gray-600 mt-2">Your journey starts here</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}{{ request()->has('redirect') ? '?redirect=' . urlencode(request()->get('redirect')) : '' }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="sr-only" />
                        <x-text-input id="email" class="block mt-1 w-full pl-10" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Email Address" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" class="sr-only" />
                        <x-text-input id="password" class="block mt-1 w-full pl-10"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" 
                                        placeholder="Password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <div class="mt-6">
                        <x-primary-button class="w-full justify-center">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>
                    
                    <!-- Registration Link -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a href="{{ route('register') }}{{ request()->has('redirect') ? '?redirect=' . urlencode(request()->get('redirect')) : '' }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                {{ __('Create Account') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
