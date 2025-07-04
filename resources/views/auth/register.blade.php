<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="max-w-4xl w-full mx-auto grid grid-cols-1 md:grid-cols-2 shadow-lg rounded-lg overflow-hidden">
            
            <!-- Left side with background image and text -->
            <div class="hidden md:block bg-cover bg-center" style="background-image: url('https://source.unsplash.com/random/800x600?bus,road');">
                <div class="flex flex-col justify-center h-full p-8 bg-gray-900 bg-opacity-60">
                    <h2 class="text-4xl font-bold text-white mb-4">Join Us Today</h2>
                    <p class="text-gray-200">Create an account to start your journey. Get access to exclusive deals and manage your trips with ease.</p>
                </div>
            </div>

            <!-- Right side with the form -->
            <div class="p-8 bg-white">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-indigo-600">BooknGo</h1>
                    <p class="text-gray-600 mt-2">Create an account to get started</p>
                </div>

                <form method="POST" action="{{ route('register') }}{{ request()->has('redirect') ? '?redirect=' . urlencode(request()->get('redirect')) : '' }}">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" class="sr-only" />
                        <x-text-input id="name" class="block mt-1 w-full pl-10" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Full Name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-4">
                        <x-input-label for="email" :value="__('Email')" class="sr-only" />
                        <x-text-input id="email" class="block mt-1 w-full pl-10" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Email Address" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" class="sr-only" />
                        <x-text-input id="password" class="block mt-1 w-full pl-10"
                                        type="password"
                                        name="password"
                                        required autocomplete="new-password"
                                        placeholder="Password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="sr-only" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full pl-10"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password"
                                        placeholder="Confirm Password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Register Button -->
                    <div class="mt-6">
                        <x-primary-button class="w-full justify-center">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a class="font-medium text-indigo-600 hover:text-indigo-500" href="{{ route('login') }}{{ request()->has('redirect') ? '?redirect=' . urlencode(request()->get('redirect')) : '' }}">
                                {{ __('Log in') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
