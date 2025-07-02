<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Dashboard | BooknGo</title>
        <meta name="description" content="Your BooknGo dashboard">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600">BooknGo</a>
                        <span class="ml-2 text-sm text-gray-500">Dashboard</span>
                    </div>
                    <nav class="flex items-center space-x-4">
                        @if(Auth::user()->isOperator() || Auth::user()->isAdmin())
                            <a href="{{ route('trips.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Trips
                            </a>
                            <a href="{{ route('buses.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Buses
                            </a>
                            <a href="{{ route('bookings.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Bookings
                            </a>
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                    Users
                                </a>
                                <a href="{{ route('admin.operators.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                    Operators
                                </a>
                            @endif
                        @else
                            <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Search Trips
                            </a>
                            <a href="{{ route('bookings.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                My Bookings
                            </a>
                        @endif
                        <div class="relative">
                            <button class="flex items-center text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out" onclick="toggleDropdown()">
                                {{ Auth::user()->name }}
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Welcome back, {{ Auth::user()->name }}!
                </h1>
                <p class="mt-2 text-gray-600">
                    @if(Auth::user()->isAdmin())
                        Manage the entire BooknGo platform from your admin dashboard.
                    @elseif(Auth::user()->isOperator())
                        Manage your buses, trips, and bookings from your operator dashboard.
                    @else
                        Find and book bus tickets for your next journey.
                    @endif
                </p>
            </div>

            @if(Auth::user()->isAdmin())
                @include('dashboard.admin')
            @elseif(Auth::user()->isOperator())
                @include('dashboard.operator')
            @else
                @include('dashboard.user')
            @endif
        </div>

        <script>
            function toggleDropdown() {
                const dropdown = document.getElementById('userDropdown');
                dropdown.classList.toggle('hidden');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('userDropdown');
                const button = event.target.closest('button');

                if (!button || !button.onclick) {
                    dropdown.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
