<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $operator->company_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex">
                        <img src="{{ $operator->logo_url }}" alt="{{ $operator->company_name }}" class="h-32 w-32">
                        <div class="ml-4">
                            <h3 class="text-2xl font-semibold">{{ $operator->company_name }}</h3>
                            <p class="text-gray-600">{{ $operator->address }}</p>
                            <p class="text-gray-600">{{ $operator->contact_info['phone'] }}</p>
                            <p class="text-gray-600">{{ $operator->contact_info['email'] }}</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h4 class="text-xl font-semibold text-gray-900 mb-6">Our Fleet & Available Trips</h4>
                        <div class="space-y-6">
                            @forelse ($operator->buses as $bus)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h5 class="text-lg font-semibold text-gray-900">{{ $bus->name }}</h5>
                                            <p class="text-gray-600">{{ $bus->registration_number }} • {{ $bus->type }}</p>
                                            <p class="text-sm text-gray-500 mt-1">{{ $bus->total_seats }} seats available</p>
                                        </div>
                                        <a href="{{ route('buses.show', $bus) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Details →
                                        </a>
                                    </div>

                                    <div class="mt-4">
                                        <h6 class="font-semibold text-gray-900 mb-3">Upcoming Trips</h6>
                                        <div class="space-y-3">
                                            @forelse ($bus->trips()->where('departure_datetime', '>', now())->orderBy('departure_datetime')->take(5)->get() as $trip)
                                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                                    <div class="flex justify-between items-center">
                                                        <div class="flex-1">
                                                            <div class="flex items-center space-x-2 mb-2">
                                                                <span class="font-medium text-gray-900">{{ $trip->route->fromCity->name }}</span>
                                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                                </svg>
                                                                <span class="font-medium text-gray-900">{{ $trip->route->toCity->name }}</span>
                                                            </div>
                                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                                <span>{{ $trip->departure_datetime->format('M d, Y • H:i A') }}</span>
                                                                <span>•</span>
                                                                <span class="font-medium text-green-600">Rs. {{ number_format($trip->price, 2) }}</span>
                                                                <span>•</span>
                                                                <span>{{ $trip->available_seats_count }} seats left</span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            @if($trip->available_seats_count > 0)
                                                                <a href="{{ route('trips.select-seats', $trip) }}"
                                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition duration-150">
                                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                    </svg>
                                                                    Book Now
                                                                </a>
                                                            @else
                                                                <span class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-600 text-sm font-medium rounded-md cursor-not-allowed">
                                                                    Sold Out
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-8">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0v10a2 2 0 002 2h4a2 2 0 002-2V7m-8 0V6a2 2 0 012-2h4a2 2 0 012 2v1"></path>
                                                    </svg>
                                                    <p class="mt-2 text-gray-500">No upcoming trips scheduled for this bus.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0v10a2 2 0 002 2h4a2 2 0 002-2V7m-8 0V6a2 2 0 012-2h4a2 2 0 012 2v1"></path>
                                    </svg>
                                    <p class="mt-2 text-lg text-gray-500">This operator has no buses yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
