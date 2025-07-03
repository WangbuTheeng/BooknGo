<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Profile</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Modern Navbar -->
        @include('components.modern-navbar')

        <!-- Page Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
                            <p class="text-gray-600">Manage your account information and preferences</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Account Settings</h2>
                        </div>
                        <nav class="p-6 space-y-2">
                            <a href="#profile-info" onclick="showSection('profile-info')" class="profile-nav-link active flex items-center px-3 py-2 text-sm font-medium rounded-md text-blue-600 bg-blue-50">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Information
                            </a>
                            <a href="#password" onclick="showSection('password')" class="profile-nav-link flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Change Password
                            </a>
                            <a href="#delete-account" onclick="showSection('delete-account')" class="profile-nav-link flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:text-red-600 hover:bg-red-50">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Account
                            </a>
                        </nav>
                    </div>

                    <!-- Account Summary -->
                    <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Account Summary</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Member Since</span>
                                <span class="text-sm text-gray-900">{{ Auth::user()->created_at->format('M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Email Verified</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ Auth::user()->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ Auth::user()->email_verified_at ? 'Verified' : 'Not Verified' }}
                                </span>
                            </div>
                            @if(Auth::user()->isUser())
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500">Total Bookings</span>
                                    <span class="text-sm text-gray-900">{{ Auth::user()->bookings()->count() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Profile Information Section -->
                    <div id="profile-info-section" class="profile-section bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Information
                            </h2>
                            <p class="text-gray-600 text-sm mt-1">Update your account's profile information and email address.</p>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div id="password-section" class="profile-section bg-white overflow-hidden shadow rounded-lg hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Update Password
                            </h2>
                            <p class="text-gray-600 text-sm mt-1">Ensure your account is using a long, random password to stay secure.</p>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account Section -->
                    <div id="delete-account-section" class="profile-section bg-white overflow-hidden shadow rounded-lg hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Account
                            </h2>
                            <p class="text-gray-600 text-sm mt-1">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Include notification component -->
    <x-notification />

    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.profile-section').forEach(section => {
                section.classList.add('hidden');
            });

            // Remove active class from all nav links
            document.querySelectorAll('.profile-nav-link').forEach(link => {
                link.classList.remove('active', 'text-blue-600', 'bg-blue-50');
                link.classList.add('text-gray-600');
            });

            // Show selected section
            document.getElementById(sectionId + '-section').classList.remove('hidden');

            // Add active class to clicked nav link
            event.target.closest('.profile-nav-link').classList.add('active', 'text-blue-600', 'bg-blue-50');
            event.target.closest('.profile-nav-link').classList.remove('text-gray-600');
        }
    </script>
</body>
</html>
