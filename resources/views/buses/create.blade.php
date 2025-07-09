<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($bus) ? 'Edit Bus' : 'Add New Bus' }} | BooknGo</title>
        <meta name="description" content="{{ isset($bus) ? 'Edit bus details' : 'Add a new bus to your fleet' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <x-modern-navbar />
            <main>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ isset($bus) ? 'Edit Bus' : 'Add New Bus' }}</h1>
                <p class="mt-2 text-gray-600">{{ isset($bus) ? 'Update bus information and features' : 'Add a new bus to your fleet with detailed information and features' }}</p>
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

            <!-- Bus Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <form method="POST" action="{{ isset($bus) ? route('buses.update', $bus) : route('buses.store') }}" class="space-y-6 p-8">
                    @csrf
                    @if(isset($bus))
                        @method('PUT')
                    @endif

                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Registration Number -->
                            <div>
                                <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Registration Number <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="registration_number"
                                    name="registration_number"
                                    value="{{ old('registration_number', isset($bus) ? $bus->registration_number : '') }}"
                                    placeholder="e.g., BA-1-PA-1234"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('registration_number') border-red-300 @enderror"
                                    required
                                >
                                @error('registration_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bus Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bus Name (Optional)
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', isset($bus) ? $bus->name : '') }}"
                                    placeholder="e.g., Express Deluxe"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bus Number -->
                            <div>
                                <label for="bus_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bus Number (Optional)
                                </label>
                                <input
                                    type="text"
                                    id="bus_number"
                                    name="bus_number"
                                    value="{{ old('bus_number', isset($bus) ? $bus->bus_number : '') }}"
                                    placeholder="e.g., 123"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bus_number') border-red-300 @enderror"
                                >
                                @error('bus_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bus Specifications -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bus Specifications</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Bus Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bus Type <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="type" 
                                    name="type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-300 @enderror"
                                    required
                                >
                                    <option value="">Select Bus Type</option>
                                    <option value="Normal" {{ old('type', isset($bus) ? $bus->type : '') === 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Deluxe" {{ old('type', isset($bus) ? $bus->type : '') === 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                                    <option value="AC" {{ old('type', isset($bus) ? $bus->type : '') === 'AC' ? 'selected' : '' }}>AC</option>
                                    <option value="Sleeper" {{ old('type', isset($bus) ? $bus->type : '') === 'Sleeper' ? 'selected' : '' }}>Sleeper</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Seats -->
                            <div>
                                <label for="total_seats" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Seats <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="total_seats"
                                    name="total_seats"
                                    value="{{ old('total_seats', isset($bus) ? $bus->total_seats : '') }}"
                                    min="1"
                                    max="100"
                                    placeholder="e.g., 40"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('total_seats') border-red-300 @enderror"
                                    required
                                >
                                @error('total_seats')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Seats will be automatically generated based on this number</p>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bus Features</h3>
                        <p class="text-sm text-gray-600 mb-4">Select all features available in this bus</p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @php
                                $availableFeatures = [
                                    'AC' => 'Air Conditioning',
                                    'WiFi' => 'WiFi',
                                    'USB Charging' => 'USB Charging Ports',
                                    'Entertainment' => 'Entertainment System',
                                    'Reclining Seats' => 'Reclining Seats',
                                    'Reading Light' => 'Reading Lights',
                                    'Blanket' => 'Blankets Provided',
                                    'Water Bottle' => 'Water Bottles',
                                    'Snacks' => 'Complimentary Snacks',
                                    'GPS Tracking' => 'GPS Tracking',
                                    'CCTV' => 'CCTV Surveillance',
                                    'Emergency Exit' => 'Emergency Exits'
                                ];
                            @endphp

                            @foreach($availableFeatures as $value => $label)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                    <input
                                        type="checkbox"
                                        name="features[]"
                                        value="{{ $value }}"
                                        {{ in_array($value, old('features', isset($bus) ? $bus->features : [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('features')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <a 
                            href="{{ route('buses.index') }}" 
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
                            @if(isset($bus))
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Bus
                            @else
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Bus
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
                        <h3 class="text-sm font-medium text-blue-800">Tips for Adding a Bus</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Registration number should follow Nepal's vehicle registration format (e.g., BA-1-PA-1234)</li>
                                <li>Choose the bus type that best matches your vehicle's comfort level</li>
                                <li>Seat count will automatically generate individual seat records for booking management</li>
                                <li>Select all applicable features to help passengers make informed booking decisions</li>
                                <li>You can edit bus details and manage seats after creation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </main>
        </div>
    </body>
</html>
