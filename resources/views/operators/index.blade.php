<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Operators') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($operators as $operator)
                            <div class="border rounded-lg p-4">
                                <a href="{{ route('operators.show', $operator) }}">
                                    <img src="{{ $operator->logo_url }}" alt="{{ $operator->company_name }}" class="h-20 mx-auto">
                                    <h3 class="text-lg font-semibold text-center mt-2">{{ $operator->company_name }}</h3>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $operators->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
