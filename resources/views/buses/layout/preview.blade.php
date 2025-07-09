<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Seat Layout Preview - {{ config('app.name', 'BooknGo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <x-modern-navbar />
        
        <!-- Page Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('buses.show', $bus) }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">🚌 Seat Layout Preview</h1>
                            <p class="text-gray-600">{{ $bus->name }} ({{ $bus->registration_number }})</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('buses.layout.configure', $bus) }}" 
                           class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 border border-blue-300 rounded-md hover:bg-blue-200">
                            Edit Layout
                        </a>
                        <a href="{{ route('buses.seats.index', $bus) }}" 
                           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            Manage Seats
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Layout Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Layout Summary</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pattern:</span>
                                <span class="font-medium">{{ strtoupper($layoutSummary['pattern']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Seats:</span>
                                <span class="font-medium">{{ $layoutSummary['total_seats'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Rows:</span>
                                <span class="font-medium">{{ $layoutSummary['rows_count'] ?? 'Auto' }}</span>
                            </div>
                            @if($layoutSummary['back_row_seats'])
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Back Row Seats:</span>
                                    <span class="font-medium">{{ $layoutSummary['back_row_seats'] }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Category:</span>
                                <span class="font-medium capitalize">{{ str_replace('_', ' ', $layoutSummary['bus_category']) }}</span>
                            </div>
                            
                            @if($layoutSummary['has_driver_side_seat'])
                                <div class="pt-2 border-t border-gray-200">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Driver-side seat available
                                    </div>
                                </div>
                            @endif
                            
                            @if($layoutSummary['has_conductor_area'])
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Conductor area included
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Seat Legend -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Seat Legend</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-blue-100 border-2 border-blue-300 rounded mr-3"></div>
                                <span class="text-sm text-gray-700">Passenger Seat</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-yellow-100 border-2 border-yellow-300 rounded mr-3"></div>
                                <span class="text-sm text-gray-700">VIP Seat</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-red-100 border-2 border-red-300 rounded mr-3"></div>
                                <span class="text-sm text-gray-700">Blocked/Not Available</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-green-100 border-2 border-green-300 rounded mr-3"></div>
                                <span class="text-sm text-gray-700">Conductor Area</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seat Layout Visualization -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Seat Layout</h2>
                            <p class="text-gray-600 text-sm mt-1">Visual representation of your bus seating arrangement</p>
                        </div>
                        <div class="p-8">
                            @if($bus->seats->count() > 0)
                                <div class="max-w-2xl mx-auto">
                                    <!-- Driver Section -->
                                    <div class="mb-8 p-4 bg-gray-100 rounded-lg text-center">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="text-gray-600 font-medium">🚗 Driver</span>
                                        </div>
                                    </div>

                                    <!-- Seats Grid -->
                                    <div class="space-y-3">
                                        @php
                                            $seatsByRow = $bus->seats->groupBy('row_number')->sortKeys();
                                        @endphp

                                        @foreach($seatsByRow as $rowNumber => $rowSeats)
                                            <div class="flex justify-center items-center space-x-2">
                                                @php
                                                    $sortedSeats = $rowSeats->sortBy('column_number');
                                                @endphp

                                                @foreach($sortedSeats as $seat)
                                                    @php
                                                        $seatClass = match($seat->seat_type) {
                                                            'vip' => 'bg-yellow-100 border-yellow-300 text-yellow-800',
                                                            'blocked' => 'bg-red-100 border-red-300 text-red-800',
                                                            'conductor' => 'bg-green-100 border-green-300 text-green-800',
                                                            'driver' => 'bg-gray-100 border-gray-300 text-gray-800',
                                                            default => 'bg-blue-100 border-blue-300 text-blue-800'
                                                        };
                                                    @endphp

                                                    <div class="relative group">
                                                        <div class="w-12 h-12 {{ $seatClass }} border-2 rounded-lg flex items-center justify-center text-sm font-medium hover:shadow-md transition duration-150 cursor-pointer"
                                                             title="{{ $seat->getDisplayName() }}">
                                                            {{ $seat->seat_number }}
                                                        </div>
                                                        
                                                        <!-- Tooltip -->
                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                            {{ $seat->getDisplayName() }}
                                                            @if($seat->price_multiplier != 1.00)
                                                                <br>Price: {{ $seat->price_multiplier }}x
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Add aisle space for certain patterns and positions -->
                                                    @if($bus->layout_pattern === '2x2' && $seat->column_number === 2)
                                                        <div class="w-6 flex items-center justify-center">
                                                            <div class="w-px h-8 bg-gray-300"></div>
                                                        </div>
                                                    @elseif($bus->layout_pattern === '2x1' && $seat->column_number === 2)
                                                        <div class="w-8 flex items-center justify-center">
                                                            <div class="w-px h-8 bg-gray-300"></div>
                                                        </div>
                                                    @elseif($bus->layout_pattern === '1x1' && $seat->column_number === 1)
                                                        <div class="w-12 flex items-center justify-center">
                                                            <div class="w-px h-8 bg-gray-300"></div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Bus Outline -->
                                    <div class="mt-8 text-center">
                                        <div class="inline-block p-4 border-2 border-gray-300 rounded-lg bg-gray-50">
                                            <span class="text-sm text-gray-600">🚪 Exit</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Seats Configured</h3>
                                    <p class="text-gray-600 mb-4">Configure your seat layout to see the preview</p>
                                    <a href="{{ route('buses.layout.configure', $bus) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Configure Layout
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Include notification component -->
    <x-notification />
</body>
</html>
