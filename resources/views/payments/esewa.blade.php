<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSewa Payment - BooknGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    eSewa Payment
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    You will be redirected to eSewa to complete your payment
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
                        <span class="text-sm text-gray-900">eSewa</span>
                    </div>
                </div>
            </div>

            <!-- eSewa Payment Form -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-center mb-4">
                    <img src="https://esewa.com.np/common/images/esewa_logo.png" alt="eSewa" class="h-8 mx-auto mb-2">
                    <p class="text-sm text-gray-600">Click the button below to proceed to eSewa</p>
                </div>

                <form action="{{ $paymentUrl }}" method="POST" id="esewaForm">
                    @foreach($formData as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Pay with eSewa
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
                            <p>Your payment is processed securely through eSewa. You will be redirected to eSewa's secure payment page.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Credentials (for development) -->
            @if(config('app.env') !== 'production')
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Test Environment
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p><strong>Test Credentials:</strong></p>
                            <p>eSewa ID: 9806800001/2/3/4/5</p>
                            <p>Password: Nepal@123</p>
                            <p>MPIN: 1122</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Auto-submit form after 3 seconds for better UX
        setTimeout(function() {
            document.getElementById('esewaForm').submit();
        }, 3000);

        // Show countdown
        let countdown = 3;
        const button = document.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        
        const countdownInterval = setInterval(function() {
            if (countdown > 0) {
                button.innerHTML = `Redirecting in ${countdown}s...`;
                countdown--;
            } else {
                button.innerHTML = originalText;
                clearInterval(countdownInterval);
            }
        }, 1000);
    </script>
</body>
</html>
