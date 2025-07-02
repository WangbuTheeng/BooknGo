<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Add Seat</title>

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
                            <h1 class="text-3xl font-bold text-gray-900">Add New Seat</h1>
                            <p class="text-gray-600">{{ $bus->name }} ({{ $bus->registration_number }})</p>
                        </div>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Seat Information
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">Add a new seat to this bus</p>
                </div>

                <form action="{{ route('buses.seats.store', $bus) }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- Seat Number -->
                        <div>
                            <label for="seat_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Seat Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="seat_number" 
                                   id="seat_number" 
                                   value="{{ old('seat_number') }}"
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
                                <option value="Front Left" {{ old('position') === 'Front Left' ? 'selected' : '' }}>Front Left</option>
                                <option value="Front Right" {{ old('position') === 'Front Right' ? 'selected' : '' }}>Front Right</option>
                                <option value="Middle Left" {{ old('position') === 'Middle Left' ? 'selected' : '' }}>Middle Left</option>
                                <option value="Middle Right" {{ old('position') === 'Middle Right' ? 'selected' : '' }}>Middle Right</option>
                                <option value="Back Left" {{ old('position') === 'Back Left' ? 'selected' : '' }}>Back Left</option>
                                <option value="Back Right" {{ old('position') === 'Back Right' ? 'selected' : '' }}>Back Right</option>
                                <option value="Window" {{ old('position') === 'Window' ? 'selected' : '' }}>Window</option>
                                <option value="Aisle" {{ old('position') === 'Aisle' ? 'selected' : '' }}>Aisle</option>
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
                                    <span class="text-gray-500">Current Seats:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ $bus->seats->count() }} / {{ $bus->total_seats }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Seats Preview -->
                        @if($bus->seats->count() > 0)
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Existing Seats</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($bus->seats->sortBy('seat_number') as $seat)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $seat->seat_number }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-xs text-gray-600">Make sure the new seat number doesn't conflict with existing ones</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Seat
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Add Multiple Seats -->
            <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Quick Add Multiple Seats
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">Automatically generate multiple seats at once</p>
                </div>

                <form action="{{ route('buses.seats.store', $bus) }}" method="POST" class="p-6" x-data="{ startNumber: '', endNumber: '', preview: [] }" x-init="$watch('startNumber', () => updatePreview()); $watch('endNumber', () => updatePreview())">
                    @csrf
                    <input type="hidden" name="bulk_create" value="1">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Number
                            </label>
                            <input type="number" 
                                   name="start_number" 
                                   id="start_number"
                                   x-model="startNumber"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                                   placeholder="1"
                                   min="1">
                        </div>

                        <div>
                            <label for="end_number" class="block text-sm font-medium text-gray-700 mb-2">
                                End Number
                            </label>
                            <input type="number" 
                                   name="end_number" 
                                   id="end_number"
                                   x-model="endNumber"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                                   placeholder="10"
                                   min="1">
                        </div>
                    </div>

                    <div class="mt-4">
                        <div x-show="startNumber && endNumber && parseInt(endNumber) >= parseInt(startNumber)" class="bg-green-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-green-900 mb-2">Preview</h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="i in (parseInt(endNumber) - parseInt(startNumber) + 1)" :key="i">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" x-text="parseInt(startNumber) + i - 1"></span>
                                </template>
                            </div>
                            <p class="mt-2 text-xs text-green-700">
                                This will create <span x-text="parseInt(endNumber) - parseInt(startNumber) + 1"></span> seats
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <button type="submit" 
                                x-show="startNumber && endNumber && parseInt(endNumber) >= parseInt(startNumber)"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Create Multiple Seats
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
