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
        <!-- Modern Navbar -->
        @include('components.modern-navbar')

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


    </body>
</html>
