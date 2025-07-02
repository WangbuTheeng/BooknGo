<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($trip) ? 'Edit Trip' : 'Schedule New Trip' }} | BooknGo</title>
        <meta name="description" content="{{ isset($trip) ? 'Edit trip details' : 'Schedule a new bus trip' }}">

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
                        <span class="ml-2 text-sm text-gray-500">{{ isset($trip) ? 'Edit Trip' : 'Schedule New Trip' }}</span>
                    </div>
                    <nav class="flex items-center space-x-4">
                        <a href="{{ route('trips.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            Back to Trips
                        </a>
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ isset($trip) ? 'Edit Trip' : 'Schedule New Trip' }}</h1>
                <p class="mt-2 text-gray-600">{{ isset($trip) ? 'Update trip information and schedule' : 'Create a new bus trip with route, schedule, and pricing details' }}</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Trip Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <form method="POST" action="{{ isset($trip) ? route('trips.update', $trip) : route('trips.store') }}" class="space-y-6 p-8">
                    @csrf
                    @if(isset($trip))
                        @method('PUT')
                    @endif

                    <!-- Route Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Route Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Route Selection -->
                            <div class="md:col-span-2">
                                <label for="route_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Route <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="route_id" 
                                    name="route_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('route_id') border-red-300 @enderror"
                                    required
                                >
                                    <option value="">Select Route</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}" {{ old('route_id', isset($trip) ? $trip->route_id : '') == $route->id ? 'selected' : '' }}>
                                            {{ $route->fromCity->name }} → {{ $route->toCity->name }}
                                            @if($route->estimated_km)
                                                ({{ $route->estimated_km }} km)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('route_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bus Selection -->
                            <div class="md:col-span-2">
                                <label for="bus_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bus <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="bus_id" 
                                    name="bus_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bus_id') border-red-300 @enderror"
                                    required
                                >
                                    <option value="">Select Bus</option>
                                    @foreach($buses as $bus)
                                        <option value="{{ $bus->id }}" {{ old('bus_id', isset($trip) ? $trip->bus_id : '') == $bus->id ? 'selected' : '' }}>
                                            {{ $bus->name ?: $bus->registration_number }} 
                                            ({{ $bus->type }} - {{ $bus->total_seats }} seats)
                                            @if(Auth::user()->isAdmin())
                                                - {{ $bus->operator->user->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('bus_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Schedule Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Departure Date & Time -->
                            <div>
                                <label for="departure_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                    Departure Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="datetime-local" 
                                    id="departure_datetime" 
                                    name="departure_datetime" 
                                    value="{{ old('departure_datetime', isset($trip) ? $trip->departure_datetime->format('Y-m-d\TH:i') : '') }}"
                                    min="{{ now()->format('Y-m-d\TH:i') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('departure_datetime') border-red-300 @enderror"
                                    required
                                >
                                @error('departure_datetime')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Arrival Time (Optional) -->
                            <div>
                                <label for="arrival_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estimated Arrival Time (Optional)
                                </label>
                                <input 
                                    type="datetime-local" 
                                    id="arrival_time" 
                                    name="arrival_time" 
                                    value="{{ old('arrival_time', isset($trip) && $trip->arrival_time ? $trip->arrival_time->format('Y-m-d\TH:i') : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('arrival_time') border-red-300 @enderror"
                                >
                                @error('arrival_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Leave empty if arrival time is not confirmed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Base Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Base Price (NPR) <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    id="price" 
                                    name="price" 
                                    value="{{ old('price', isset($trip) ? $trip->price : '') }}"
                                    min="0" 
                                    step="0.01"
                                    placeholder="e.g., 1500"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-300 @enderror"
                                    required
                                >
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Festival Fare -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Festival Pricing</label>
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        id="is_festival_fare" 
                                        name="is_festival_fare" 
                                        value="1"
                                        {{ old('is_festival_fare', isset($trip) ? $trip->is_festival_fare : false) ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    >
                                    <label for="is_festival_fare" class="ml-2 text-sm text-gray-700">
                                        Apply festival fare pricing
                                    </label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Festival pricing applies a multiplier to the base price</p>
                            </div>

                            @if(isset($trip))
                                <!-- Status (Only for editing) -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Trip Status <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="status" 
                                        name="status" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror"
                                        required
                                    >
                                        <option value="active" {{ old('status', $trip->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="cancelled" {{ old('status', $trip->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="completed" {{ old('status', $trip->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <a 
                            href="{{ route('trips.index') }}" 
                            class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancel
                        </a>
                        
                        <button 
                            type="submit"
                            class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            @if(isset($trip))
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Trip
                            @else
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Schedule Trip
                            @endif
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Tips for Scheduling Trips</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Select the appropriate route and ensure your bus is available for the scheduled time</li>
                                <li>Set departure time at least 1 hour from now to allow for booking preparation</li>
                                <li>Festival fare pricing automatically applies a multiplier to increase revenue during peak seasons</li>
                                <li>Arrival time is optional but helps passengers plan their journey better</li>
                                <li>You can edit trip details until passengers start booking seats</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
