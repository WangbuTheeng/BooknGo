<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BooknGo - Festival Bus Booking Made Easy</title>
        <meta name="description" content="Book bus tickets online for Nepal's festival season. Easy booking, real-time seat selection, and secure payments.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            /* Custom scrollbar for a cleaner look */
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            ::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 10px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #555;
            }
        </style>

        <!-- Styles / Scripts (Keep these for Laravel integration) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-sans antialiased">
        <!-- Header -->
        <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center">
                        <h1 class="text-3xl font-extrabold text-blue-700 tracking-tight">BooknGo</h1>
                        <span class="ml-3 text-base text-gray-600 font-medium hidden md:block">Festival Bus Booking</span>
                    </div>
                    @if (Route::has('login'))
                        <nav class="flex items-center space-x-4">
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="inline-flex items-center px-5 py-2 border border-transparent text-base font-medium rounded-full text-white bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out shadow-md hover:shadow-lg"
                                >
                                    Dashboard
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="text-gray-600 hover:text-blue-700 px-4 py-2 rounded-full text-base font-medium transition duration-200 ease-in-out"
                                >
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="inline-flex items-center px-5 py-2 border border-transparent text-base font-medium rounded-full text-white bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out shadow-md hover:shadow-lg"
                                    >
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <div class="relative bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 overflow-hidden py-24 md:py-32">
            <div class="absolute inset-0 bg-black opacity-30"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 leading-tight drop-shadow-lg">
                        Festival Travel Made
                        <span class="text-yellow-400">Simple</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed">
                        Book your bus tickets online for Nepal's vibrant festival season. Skip the queues, choose your seats, and travel with confidence.
                    </p>
                </div>

                <!-- Search Form -->
                <div class="max-w-5xl mx-auto -mb-24 md:-mb-32 relative z-10">
                    <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-blue-200">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Find Your Perfect Journey</h2>
                        
                        <form action="{{ route('trips.search') }}" method="GET" class="space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                                <!-- From City -->
                                <div>
                                    <label for="from_city_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="inline-block w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                                        From
                                    </label>
                                    <select 
                                        name="from_city_id" 
                                        id="from_city_id" 
                                        required
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-3 focus:ring-blue-400 focus:border-blue-500 transition duration-200 ease-in-out text-gray-800 shadow-sm"
                                    >
                                        <option value="">Select departure city</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- To City -->
                                <div>
                                    <label for="to_city_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="inline-block w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                                        To
                                    </label>
                                    <select 
                                        name="to_city_id" 
                                        id="to_city_id" 
                                        required
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-3 focus:ring-blue-400 focus:border-blue-500 transition duration-200 ease-in-out text-gray-800 shadow-sm"
                                    >
                                        <option value="">Select destination city</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Departure Date -->
                                <div>
                                    <label for="departure_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="inline-block w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11zM5 7V6h14v1H5z"/></svg>
                                        Departure Date
                                    </label>
                                    <input 
                                        type="date" 
                                        name="departure_date" 
                                        id="departure_date" 
                                        required
                                        min="{{ date('Y-m-d') }}"
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-3 focus:ring-blue-400 focus:border-blue-500 transition duration-200 ease-in-out text-gray-800 shadow-sm"
                                    >
                                </div>
                            </div>

                            <!-- Search Button -->
                            <div class="text-center pt-4">
                                <button 
                                    type="submit"
                                    class="inline-flex items-center px-10 py-4 border border-transparent text-xl font-bold rounded-full text-white bg-gradient-to-r from-green-500 to-teal-600 hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-3 focus:ring-offset-2 focus:ring-green-400 transform hover:scale-105 transition duration-300 ease-in-out shadow-xl"
                                >
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Search Buses
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Why Choose BooknGo?</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Experience the easiest way to book bus tickets for Nepal's festival season with unparalleled convenience and reliability.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <!-- Feature 1 -->
                    <div class="text-center p-8 rounded-2xl bg-blue-50 hover:bg-blue-100 transition duration-300 ease-in-out transform hover:-translate-y-2 shadow-lg">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Real-time Booking</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Book tickets instantly with real-time seat availability and receive instant confirmation for your peace of mind.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="text-center p-8 rounded-2xl bg-green-50 hover:bg-green-100 transition duration-300 ease-in-out transform hover:-translate-y-2 shadow-lg">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-600 to-teal-700 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Secure Payments</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Pay safely and conveniently with popular local payment gateways like eSewa and Khalti.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center p-8 rounded-2xl bg-purple-50 hover:bg-purple-100 transition duration-300 ease-in-out transform hover:-translate-y-2 shadow-lg">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Choose Your Seat</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Select your preferred seat with ease using our intuitive and interactive seat map.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Routes Section -->
        <div class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Popular Festival Routes</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Discover the most traveled routes and secure your journey during Nepal's peak festival season.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Route 1 -->
                    <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 p-8 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-bold text-gray-900">Kathmandu <span class="text-blue-600">→</span> Pokhara</h3>
                            <span class="text-base text-blue-600 font-semibold bg-blue-50 px-3 py-1 rounded-full">6-7 hours</span>
                        </div>
                        <p class="text-gray-600 mb-6 leading-relaxed">Experience the scenic route to Nepal's beautiful lake city, Pokhara.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-3xl font-extrabold text-green-600">Rs. 800<span class="text-lg">+</span></span>
                            <a href="#" class="inline-flex items-center text-lg font-semibold text-blue-600 hover:text-blue-800 transition duration-200 ease-in-out">
                                View Buses 
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Route 2 -->
                    <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 p-8 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-bold text-gray-900">Kathmandu <span class="text-blue-600">→</span> Chitwan</h3>
                            <span class="text-base text-blue-600 font-semibold bg-blue-50 px-3 py-1 rounded-full">4-5 hours</span>
                        </div>
                        <p class="text-gray-600 mb-6 leading-relaxed">Journey to the heart of Nepal's wildlife, a perfect getaway for nature lovers.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-3xl font-extrabold text-green-600">Rs. 600<span class="text-lg">+</span></span>
                            <a href="#" class="inline-flex items-center text-lg font-semibold text-blue-600 hover:text-blue-800 transition duration-200 ease-in-out">
                                View Buses 
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Route 3 -->
                    <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 ease-in-out transform hover:-translate-y-1 p-8 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-bold text-gray-900">Kathmandu <span class="text-blue-600">→</span> Biratnagar</h3>
                            <span class="text-base text-blue-600 font-semibold bg-blue-50 px-3 py-1 rounded-full">8-9 hours</span>
                        </div>
                        <p class="text-gray-600 mb-6 leading-relaxed">Travel to the bustling commercial hub of Eastern Nepal, connecting you to key regions.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-3xl font-extrabold text-green-600">Rs. 1200<span class="text-lg">+</span></span>
                            <a href="#" class="inline-flex items-center text-lg font-semibold text-blue-600 hover:text-blue-800 transition duration-200 ease-in-out">
                                View Buses 
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-3xl font-extrabold text-blue-400 mb-5">BooknGo</h3>
                        <p class="text-gray-300 mb-6 leading-relaxed">
                            Making festival travel easier for everyone in Nepal. Book your bus tickets online with confidence and convenience, ensuring a smooth journey every time.
                        </p>
                        <div class="flex space-x-6">
                            <a href="#" class="text-gray-400 hover:text-white transition duration-200 transform hover:scale-110">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition duration-200 transform hover:scale-110">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition duration-200 transform hover:scale-110">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 10v4h3v7h4v-7h3l1-4h-4V7a1 1 0 011-1h3V2H7a5 5 0 00-5 5v3z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-xl font-bold mb-5 text-gray-200">Quick Links</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-300 hover:text-blue-300 transition duration-150">About Us</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-300 transition duration-150">Contact</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-300 transition duration-150">Help Center</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-300 transition duration-150">Terms of Service</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-xl font-bold mb-5 text-gray-200">Support</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-300 hover:text-blue-300 transition duration-150">Customer Service</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-300 transition duration-150">Booking Help</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-300 transition duration-150">Cancellation</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-blue-300 transition duration-150">Refunds</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                    <p class="text-gray-400 text-sm">
                        &copy; {{ date('Y') }} BooknGo. All rights reserved. Made with ❤️ for Nepal's travelers.
                    </p>
                </div>
            </div>
        </footer>
    </body>
</html>
