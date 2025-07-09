<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Configure Bus Seat Layout - {{ config('app.name', 'BooknGo') }}</title>

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
                            <h1 class="text-3xl font-bold text-gray-900">🚌 Configure Seat Layout</h1>
                            <p class="text-gray-600">{{ $bus->name }} ({{ $bus->registration_number }})</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-blue-900 mb-4">🪑 Seat Configuration Instructions</h2>
                <div class="text-blue-800 space-y-2">
                    <p>• Enter total number of passenger seats (excluding driver and conductor)</p>
                    <p>• Select your seat layout pattern per row:</p>
                    <div class="ml-4 space-y-1">
                        <p><strong>2×2:</strong> Two seats on both left and right (standard)</p>
                        <p><strong>2×1:</strong> Two seats on one side, one seat on the other (deluxe or night bus)</p>
                        <p><strong>1×1:</strong> Single seat both sides (sleeper or VIP bus)</p>
                        <p><strong>Custom:</strong> Fully customize layout row by row</p>
                    </div>
                    <p>• Configure special seats like VIP, blocked, or conductor areas</p>
                    <p>• Preview your layout before saving</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Configuration Form -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Layout Configuration</h2>
                    </div>
                    
                    <form method="POST" action="{{ route('buses.layout.store', $bus) }}" class="p-6 space-y-6" 
                          x-data="seatLayoutConfig()" x-init="init()">
                        @csrf

                        <!-- Basic Configuration -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Configuration</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Total Seats -->
                                <div>
                                    <label for="total_seats" class="block text-sm font-medium text-gray-700 mb-2">
                                        Total Passenger Seats <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           name="total_seats" 
                                           id="total_seats"
                                           x-model="config.total_seats"
                                           @input="updatePreview()"
                                           min="1" max="100"
                                           value="{{ old('total_seats', $bus->total_seats) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('total_seats') border-red-300 @enderror"
                                           required>
                                    @error('total_seats')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Layout Pattern -->
                                <div>
                                    <label for="layout_pattern" class="block text-sm font-medium text-gray-700 mb-2">
                                        Layout Pattern <span class="text-red-500">*</span>
                                    </label>
                                    <select name="layout_pattern" 
                                            id="layout_pattern"
                                            x-model="config.layout_pattern"
                                            @change="updatePreview()"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('layout_pattern') border-red-300 @enderror"
                                            required>
                                        <option value="">Select Pattern</option>
                                        <option value="2x2" {{ old('layout_pattern', $bus->layout_pattern) === '2x2' ? 'selected' : '' }}>2×2 Standard</option>
                                        <option value="2x1" {{ old('layout_pattern', $bus->layout_pattern) === '2x1' ? 'selected' : '' }}>2×1 Deluxe</option>
                                        <option value="1x1" {{ old('layout_pattern', $bus->layout_pattern) === '1x1' ? 'selected' : '' }}>1×1 VIP/Sleeper</option>
                                        <option value="custom" {{ old('layout_pattern', $bus->layout_pattern) === 'custom' ? 'selected' : '' }}>Custom Layout</option>
                                    </select>
                                    @error('layout_pattern')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Configuration -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Advanced Configuration</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Rows Count -->
                                <div>
                                    <label for="rows_count" class="block text-sm font-medium text-gray-700 mb-2">
                                        Number of Rows
                                    </label>
                                    <input type="number" 
                                           name="rows_count" 
                                           id="rows_count"
                                           x-model="config.rows_count"
                                           @input="updatePreview()"
                                           min="1" max="20"
                                           value="{{ old('rows_count', $bus->rows_count) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <p class="mt-1 text-sm text-gray-500">Leave empty for auto-calculation</p>
                                </div>

                                <!-- Back Row Seats -->
                                <div>
                                    <label for="back_row_seats" class="block text-sm font-medium text-gray-700 mb-2">
                                        Back Row Seats
                                    </label>
                                    <select name="back_row_seats" 
                                            id="back_row_seats"
                                            x-model="config.back_row_seats"
                                            @change="updatePreview()"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="0">No back row</option>
                                        <option value="5" {{ old('back_row_seats', $bus->back_row_seats) == 5 ? 'selected' : '' }}>5 seats</option>
                                        <option value="6" {{ old('back_row_seats', $bus->back_row_seats) == 6 ? 'selected' : '' }}>6 seats</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bus Category -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bus Category</h3>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @php
                                    $categories = [
                                        'standard' => 'Standard',
                                        'deluxe' => 'Deluxe',
                                        'sleeper' => 'Sleeper',
                                        'semi_sleeper' => 'Semi-Sleeper',
                                        'vip' => 'VIP'
                                    ];
                                @endphp
                                
                                @foreach($categories as $value => $label)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" 
                                               name="bus_category" 
                                               value="{{ $value }}"
                                               {{ old('bus_category', $bus->bus_category ?? 'standard') === $value ? 'checked' : '' }}
                                               class="mr-3 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Special Features -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Special Features</h3>
                            
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="has_driver_side_seat" 
                                           value="1"
                                           {{ old('has_driver_side_seat', $bus->has_driver_side_seat) ? 'checked' : '' }}
                                           class="mr-3 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Has driver-side seat</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="driver_side_seat_usable" 
                                           value="1"
                                           {{ old('driver_side_seat_usable', $bus->driver_side_seat_usable ?? true) ? 'checked' : '' }}
                                           class="mr-3 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Driver-side seat is usable by passengers</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="has_conductor_area" 
                                           value="1"
                                           {{ old('has_conductor_area', $bus->has_conductor_area) ? 'checked' : '' }}
                                           class="mr-3 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Has conductor/standing area</span>
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('buses.show', $bus) }}" 
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancel
                            </a>
                            
                            <div class="flex space-x-3">
                                <button type="button" 
                                        @click="updatePreview()"
                                        class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 border border-blue-300 rounded-md hover:bg-blue-200">
                                    Preview Layout
                                </button>
                                
                                <button type="submit" 
                                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                    Save Configuration
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Live Preview -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Layout Preview</h2>
                    </div>
                    
                    <div class="p-6">
                        <div id="layout-preview" class="text-center">
                            <div class="text-gray-500">Configure your layout to see preview</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Include notification component -->
    <x-notification />

    <script>
        function seatLayoutConfig() {
            return {
                config: {
                    total_seats: {{ old('total_seats', $bus->total_seats ?? 40) }},
                    layout_pattern: '{{ old('layout_pattern', $bus->layout_pattern ?? '2x2') }}',
                    rows_count: {{ old('rows_count', $bus->rows_count ?? 'null') }},
                    back_row_seats: {{ old('back_row_seats', $bus->back_row_seats ?? 0) }}
                },
                
                init() {
                    this.updatePreview();
                },
                
                async updatePreview() {
                    try {
                        const response = await fetch(`{{ route('buses.layout.preview-data', $bus) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.config)
                        });
                        
                        const data = await response.json();
                        this.renderPreview(data);
                    } catch (error) {
                        console.error('Preview update failed:', error);
                    }
                },
                
                renderPreview(data) {
                    const previewContainer = document.getElementById('layout-preview');
                    
                    if (!data.seats || data.seats.length === 0) {
                        previewContainer.innerHTML = '<div class="text-gray-500">No seats to preview</div>';
                        return;
                    }
                    
                    let html = `
                        <div class="max-w-md mx-auto">
                            <!-- Driver Section -->
                            <div class="mb-4 p-3 bg-gray-100 rounded-lg text-center">
                                <span class="text-sm font-medium text-gray-600">🚗 Driver</span>
                            </div>
                            
                            <!-- Seats Grid -->
                            <div class="space-y-2">
                    `;
                    
                    // Group seats by row
                    const seatsByRow = {};
                    data.seats.forEach(seat => {
                        if (!seatsByRow[seat.row_number]) {
                            seatsByRow[seat.row_number] = [];
                        }
                        seatsByRow[seat.row_number].push(seat);
                    });
                    
                    // Render each row
                    Object.keys(seatsByRow).sort((a, b) => parseInt(a) - parseInt(b)).forEach(rowNum => {
                        const rowSeats = seatsByRow[rowNum].sort((a, b) => a.column_number - b.column_number);
                        
                        html += '<div class="flex justify-center items-center space-x-2">';
                        
                        rowSeats.forEach(seat => {
                            const seatClass = seat.seat_type === 'vip' ? 'bg-yellow-100 border-yellow-300 text-yellow-800' : 
                                            seat.seat_type === 'blocked' ? 'bg-red-100 border-red-300 text-red-800' :
                                            'bg-blue-100 border-blue-300 text-blue-800';
                            
                            html += `
                                <div class="w-10 h-10 ${seatClass} border-2 rounded flex items-center justify-center text-xs font-medium">
                                    ${seat.seat_number}
                                </div>
                            `;
                            
                            // Add aisle space for certain patterns
                            if (data.pattern === '2x2' && seat.column_number === 2) {
                                html += '<div class="w-4"></div>'; // Aisle space
                            } else if (data.pattern === '2x1' && seat.column_number === 2) {
                                html += '<div class="w-6"></div>'; // Aisle space
                            }
                        });
                        
                        html += '</div>';
                    });
                    
                    html += `
                            </div>
                            
                            <!-- Summary -->
                            <div class="mt-4 text-sm text-gray-600 text-center">
                                <p>Pattern: ${data.pattern.toUpperCase()} | Total Seats: ${data.total_seats} | Rows: ${data.rows_count}</p>
                            </div>
                        </div>
                    `;
                    
                    previewContainer.innerHTML = html;
                }
            }
        }
    </script>
</body>
</html>
