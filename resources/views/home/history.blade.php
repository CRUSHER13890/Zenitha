<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Purchase History - BYU-CARE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
<div class="container-lg h-screen overflow-hidden">
    <!-- Navbar Start -->
    @include('home.navbar', ['title' => 'History'])
    <!-- Navbar End -->

    <!-- Content Start -->
    <div class="flex-1 p-4 pt-20">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        
        @if (count($history) == 0)
            <div class="flex items-center justify-center h-full flex-col">
                <h1 class="text-6xl font-bold text-yellow-400">History is Empty</h1>
                <a href="{{ route('home.products') }}"
                    class="border-2 border-gray-500 rounded-md bg-yellow-400 px-5 py-2 mt-2 hover:bg-yellow-500">Explore</a>
            </div>
        @else
            <div class="flex gap-2 mb-4">
                <a href="{{ route('home.products') }}"
                    class="px-5 py-2 border bg-gray-700 text-center text-white rounded-md">Kembali</a>
                <a href="{{ route('check-status') }}"
                    class="px-5 py-2 border bg-yellow-500 text-center text-white rounded-md">Check Status</a>
            </div>
            
            @foreach ($history as $h)
                <div class="grid grid-cols-1 pt-3">
                    <div class="bg-white p-4 shadow-md flex justify-between items-center rounded-lg">
                        <div>
                            <h1 class="font-bold">{{ $h->no_penjualan }}</h1>
                            <h1>{{ date('d F Y', strtotime($h->tgl_penjualan)) }}</h1>
                            <p>{{ $h->daftar_barang }}</p>
                            <p class="{{ $h->status_pembayaran == 'lunas' ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ ucwords(str_replace('_', ' ', $h->status_pembayaran)) }}
                            </p>
                        </div>
                        <div class="flex items-center">
                            <h1 class="mr-4 font-bold">Rp. {{ number_format($h->total, 0, ',', '.') }}</h1>
                            @if ($h->status_pembayaran != 'lunas')
                                <button id="pay-button-{{ $h->id }}"
                                    class="pay-button px-5 py-2 border bg-yellow-500 text-center text-white rounded-md hover:bg-yellow-600"
                                    data-id="{{ $h->id }}">Pay</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <!-- Content End -->
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-5 rounded-lg shadow-lg">
        <div class="flex items-center">
            <svg class="animate-spin h-5 w-5 mr-3 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Processing payment...</span>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="error-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900">Payment Error</h3>
        </div>
        <p id="error-message" class="text-sm text-gray-500 mb-4"></p>
        <div class="flex justify-end">
            <button id="close-error-modal" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Close</button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900">Payment Successful</h3>
        </div>
        <p class="text-sm text-gray-500 mb-4">Your payment has been processed successfully!</p>
        <div class="flex justify-end">
            <button id="close-success-modal" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">OK</button>
        </div>
    </div>
</div>

<!-- Midtrans Snap Script -->
<script type="text/javascript" 
    src="{{ env('MIDTRANS_IS_PRODUCTION', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
    data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const loadingOverlay = document.getElementById('loading-overlay');
        const errorModal = document.getElementById('error-modal');
        const successModal = document.getElementById('success-modal');
        const errorMessage = document.getElementById('error-message');
        
        // Close modal functions
        document.getElementById('close-error-modal').addEventListener('click', function() {
            errorModal.classList.add('hidden');
        });
        
        document.getElementById('close-success-modal').addEventListener('click', function() {
            successModal.classList.add('hidden');
            window.location.reload(); // Refresh to update payment status
        });
        
        // Show error modal
        function showError(message) {
            errorMessage.textContent = message;
            errorModal.classList.remove('hidden');
        }
        
        // Show success modal
        function showSuccess() {
            successModal.classList.remove('hidden');
        }
        
        // Handle pay button clicks
        document.querySelectorAll('.pay-button').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const payButton = this;
                
                // Disable button during processing
                payButton.disabled = true;
                payButton.textContent = 'Processing...';
                loadingOverlay.classList.remove('hidden');
                
                fetch(`/snap-token/${id}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    loadingOverlay.classList.add('hidden');
                    
                    // Re-enable button
                    payButton.disabled = false;
                    payButton.textContent = 'Pay';
                    
                    if (data.error) {
                        showError('Error: ' + (data.message || 'Unknown error occurred'));
                        return;
                    }
                    
                    if (!data.snapToken) {
                        showError('Failed to get payment token. Please try again.');
                        return;
                    }
                    
                    // Initialize Midtrans Snap
                    window.snap.pay(data.snapToken, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            showSuccess();
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            showError('Payment pending. Please complete your payment.');
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            showError('Payment failed. Please try again.');
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                            // User closed the popup without completing payment
                        }
                    });
                })
                .catch(error => {
                    loadingOverlay.classList.add('hidden');
                    
                    // Re-enable button
                    payButton.disabled = false;
                    payButton.textContent = 'Pay';
                    
                    console.error('Error:', error);
                    showError('Failed to process payment: ' + error.message);
                });
            });
        });
    });
</script>
</body>
</html>