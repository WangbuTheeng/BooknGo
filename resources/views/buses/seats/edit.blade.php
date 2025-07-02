<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Edit Seat</title>

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
                        <a href="{{ route('buses.seats.index', $bus) }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Edit Seat {{ $seat->seat_number }}</h1>
                            <p class="text-gray-600">{{ $bus->name }} ({{ $bus->registration_number }})</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('buses.seats.show', [$bus, $seat]) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Seat Information
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">Update seat details for this bus</p>
                </div>

                <form action="{{ route('buses.seats.update', [$bus, $seat]) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Seat Number -->
                        <div>
                            <label for="seat_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Seat Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="seat_number" 
                                   id="seat_number" 
                                   value="{{ old('seat_number', $seat->seat_number) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('seat_number') border-red-500 @enderror"
                                   placeholder="e.g., 1, A1, 2B"
                                   required>
                            @error('seat_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Enter a unique seat number for this bus</p>
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                Position
                            </label>
                            <select name="position" 
                                    id="position" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror">
                                <option value="">Select position (optional)</option>
                                <option value="Front Left" {{ old('position', $seat->position) === 'Front Left' ? 'selected' : '' }}>Front Left</option>
                                <option value="Front Right" {{ old('position', $seat->position) === 'Front Right' ? 'selected' : '' }}>Front Right</option>
                                <option value="Middle Left" {{ old('position', $seat->position) === 'Middle Left' ? 'selected' : '' }}>Middle Left</option>
                                <option value="Middle Right" {{ old('position', $seat->position) === 'Middle Right' ? 'selected' : '' }}>Middle Right</option>
                                <option value="Back Left" {{ old('position', $seat->position) === 'Back Left' ? 'selected' : '' }}>Back Left</option>
                                <option value="Back Right" {{ old('position', $seat->position) === 'Back Right' ? 'selected' : '' }}>Back Right</option>
                                <option value="Window" {{ old('position', $seat->position) === 'Window' ? 'selected' : '' }}>Window</option>
                                <option value="Aisle" {{ old('position', $seat->position) === 'Aisle' ? 'selected' : '' }}>Aisle</option>
                            </select>
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Specify the seat's position in the bus (optional)</p>
                        </div>

                        <!-- Bus Information Display -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Bus Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Bus Name:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $bus->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Registration:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $bus->registration_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Type:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $bus->type }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Total Seats:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $bus->seats->count() }} / {{ $bus->total_seats }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Seat Info -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Current Seat Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Current Number:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $seat->seat_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Current Position:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $seat->position ?? 'Not specified' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Created:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $seat->created_at->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Last Updated:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $seat->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Other Seats Preview -->
                        @if($bus->seats->where('id', '!=', $seat->id)->count() > 0)
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Other Seats in this Bus</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($bus->seats->where('id', '!=', $seat->id)->sortBy('seat_number') as $otherSeat)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $otherSeat->seat_number }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-xs text-yellow-700">Make sure the seat number doesn't conflict with existing ones</p>
                            </div>
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex items-center justify-end space-x-3">
                        <a href="{{ route('buses.seats.index', $bus) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Seat
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Include notification component -->
    <x-notification />
</body>
</html>
