<div x-data="{ showModal: false }">
    <button @click="showModal = true" class="w-full bg-gray-700 hover:bg-gray-600 text-white py-3 px-4 rounded-lg font-medium transition duration-150">
        VIEW TICKET HISTORY
    </button>

    <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.away="showModal = false">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Ticket History</h3>
            <div class="text-gray-600">
                <p>Here is a list of your past tickets:</p>
                <ul>
                    <!-- Ticket history will be populated here -->
                </ul>
            </div>
            <button @click="showModal = false" class="mt-4 w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition duration-150">
                Close
            </button>
        </div>
    </div>
</div>
