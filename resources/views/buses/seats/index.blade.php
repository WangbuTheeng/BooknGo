<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Seat Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Page Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('buses.index') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Seat Management</h1>
                            <p class="text-gray-600">{{ $bus->name }} ({{ $bus->registration_number }})</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('buses.seats.create', $bus) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Seat
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Bus Information -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Bus Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $bus->total_seats }}</div>
                            <div class="text-sm text-gray-500">Total Seats</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $bus->seats->count() }}</div>
                            <div class="text-sm text-gray-500">Configured Seats</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $bus->type }}</div>
                            <div class="text-sm text-gray-500">Bus Type</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $bus->operator->user->name }}</div>
                            <div class="text-sm text-gray-500">Operator</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seat Layout -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Seat Layout</h2>
                    <p class="text-gray-600 text-sm mt-1">Visual representation of seat arrangement</p>
                </div>
                <div class="p-6">
                    @if($bus->seats->count() > 0)
                        <!-- Seat Grid -->
                        <div class="max-w-md mx-auto">
                            <!-- Driver Section -->
                            <div class="mb-6 p-4 bg-gray-100 rounded-lg">
                                <div class="flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-gray-600 font-medium">Driver</span>
                                </div>
                            </div>

                            <!-- Seats Grid -->
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($bus->seats->sortBy('seat_number') as $seat)
                                    <div class="relative group">
                                        <div class="w-12 h-12 bg-blue-100 border-2 border-blue-300 rounded-lg flex items-center justify-center text-sm font-medium text-blue-800 hover:bg-blue-200 transition duration-150 cursor-pointer"
                                             onclick="showSeatDetails('{{ $seat->id }}', '{{ $seat->seat_number }}', '{{ $seat->position }}')">
                                            {{ $seat->seat_number }}
                                        </div>
                                        
                                        <!-- Seat Actions Dropdown -->
                                        <div class="absolute top-0 right-0 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs hover:bg-gray-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"></path>
                                                    </svg>
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-32 bg-white rounded-md shadow-lg z-10">
                                                    <div class="py-1">
                                                        <a href="{{ route('buses.seats.edit', [$bus, $seat]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>
                                                        <form action="{{ route('buses.seats.destroy', [$bus, $seat]) }}" method="POST" class="block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Are you sure?')" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Legend -->
                            <div class="mt-6 flex items-center justify-center space-x-4 text-sm">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded mr-2"></div>
                                    <span class="text-gray-600">Available Seat</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No seats configured</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding seats to this bus.</p>
                            <div class="mt-6">
                                <a href="{{ route('buses.seats.create', $bus) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add First Seat
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Seat List Table -->
            @if($bus->seats->count() > 0)
                <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Seat Details</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seat Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bookings</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bus->seats->sortBy('seat_number') as $seat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-sm font-medium text-blue-800 mr-3">
                                                    {{ $seat->seat_number }}
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">Seat {{ $seat->seat_number }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $seat->position ?: 'Not specified' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $seat->bookingSeats->count() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('buses.seats.show', [$bus, $seat]) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('buses.seats.edit', [$bus, $seat]) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('buses.seats.destroy', [$bus, $seat]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure you want to delete this seat?')" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Include notification component -->
    <x-notification />

    <script>
        function showSeatDetails(seatId, seatNumber, position) {
            window.showNotification(`Seat ${seatNumber} - Position: ${position || 'Not specified'}`, 'info', 'Seat Details');
        }
    </script>
</body>
</html>
