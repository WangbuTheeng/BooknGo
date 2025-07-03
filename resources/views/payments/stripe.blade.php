<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment - BooknGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Stripe Payment
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter your card details to complete the payment
                </p>
            </div>

            <!-- Payment Details Card -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Booking Reference</span>
                        <span class="text-sm text-gray-900">{{ $payment->booking->booking_reference }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Transaction ID</span>
                        <span class="text-sm text-gray-900">{{ $payment->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Amount</span>
                        <span class="text-sm font-semibold text-gray-900">Rs. {{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Payment Method</span>
                        <span class="text-sm text-gray-900">Credit/Debit Card</span>
                    </div>
                </div>
            </div>

            <!-- Stripe Payment Form -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-center mb-4">
                    <div class="flex items-center justify-center mb-2">
                        <span class="text-blue-600 font-bold text-lg">Stripe</span>
                    </div>
                    <p class="text-sm text-gray-600">Enter your card details below</p>
                </div>

                <form id="payment-form">
                    <div id="payment-element" class="mb-4">
                        <!-- Stripe Elements will create form elements here -->
                    </div>
                    
                    <div id="payment-errors" class="text-red-600 text-sm mb-4 hidden"></div>
                    
                    <button id="submit-button" type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <span id="button-text">Pay Rs. {{ number_format($payment->amount, 2) }}</span>
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('bookings.show', $payment->booking) }}" class="text-sm text-gray-500 hover:text-gray-700">
                        ← Back to booking
                    </a>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Secure Payment
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Your payment is processed securely through Stripe. Your card details are encrypted and never stored on our servers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const stripe = Stripe('{{ $publishableKey }}');
        const elements = stripe.elements({
            clientSecret: '{{ $paymentIntent['client_secret'] }}'
        });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const errorElement = document.getElementById('payment-errors');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            submitButton.disabled = true;
            buttonText.textContent = 'Processing...';
            errorElement.classList.add('hidden');

            const {error} = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: '{{ route("payments.stripe.success") }}?payment_intent={{ $paymentIntent["payment_intent_id"] }}',
                },
            });

            if (error) {
                errorElement.textContent = error.message;
                errorElement.classList.remove('hidden');
                submitButton.disabled = false;
                buttonText.textContent = 'Pay Rs. {{ number_format($payment->amount, 2) }}';
            }
        });

        // Handle real-time validation errors from the payment Element
        paymentElement.on('change', ({error}) => {
            if (error) {
                errorElement.textContent = error.message;
                errorElement.classList.remove('hidden');
            } else {
                errorElement.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
