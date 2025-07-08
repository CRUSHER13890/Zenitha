<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reservations</title>
    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="container-lg min-h-screen pb-8">
        <!-- Navbar Start -->
        @include('home.navbar', ['title' => 'Reservations'])
        <!-- Navbar End -->

        <!-- Sidebar Start -->
        <div class="border-r border-gray-300 h-full w-60 fixed mt-16 bg-white">
            <div class="p-4">
                <h1 class="text-xl font-bold">Categories</h1>
                <ul class="mt-4">
                    <li class="mb-2">
                        <a href="{{ route('home.products') }}" class="text-gray-400 hover:text-yellow-400 transition duration-300">Products</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('home.reservations') }}"
                            class="{{ Route::is('home.reservations') ? 'text-yellow-400 font-medium' : 'text-gray-400 hover:text-yellow-400 transition duration-300' }}">Reservations</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="flex-1 p-4 pl-64 pt-20">
            <!-- Alerts -->
            @if ($message = Session::get('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow">
                    <p>{{ $message }}</p>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded shadow">
                    <p>{{ $message }}</p>
                </div>
            @endif
            
            <h1 class="text-2xl font-bold mb-4">Available Services</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($layanan as $b)
                    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300 flex flex-col justify-between">
                        <div>
                            <h1 class="text-xl font-bold">{{ $b->nama_layanan }}</h1>
                            <p class="text-gray-600 mt-2">{{ $b->deskripsi ?? 'Layanan perawatan profesional untuk memenuhi kebutuhan kecantikan dan kesehatan Anda.' }}</p>
                            <p class="text-yellow-500 font-bold text-xl mt-3">Rp.
                                {{ number_format($b->harga_layanan, 0, ',', '.') }}
                            </p>
                        </div>
                        @if (Auth::check())
                            <div class="mt-4">
                                <a href="{{ route('home.add-reservasi', $b->id) }}"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-6 py-2 rounded-md inline-flex items-center transition duration-300">
                                    <i class="mdi mdi-calendar-check mr-2"></i> Reservasi
                                </a>
                            </div>
                        @else
                            <div class="mt-4">
                                <a href="{{ route('login') }}"
                                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-md inline-flex items-center transition duration-300">
                                    <i class="mdi mdi-login mr-2"></i> Login to Reserve
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            @if(count($layanan) == 0)
                <div class="bg-white p-8 rounded-lg shadow-md text-center">
                    <i class="mdi mdi-calendar-remove text-5xl text-gray-400"></i>
                    <p class="text-gray-500 mt-4">No services available at the moment.</p>
                </div>
            @endif
        </div>
        <!-- Content End -->
    </div>
</body>

</html>