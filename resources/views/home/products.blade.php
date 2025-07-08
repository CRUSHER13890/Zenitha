<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products</title>
    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container-lg h-screen">
        <!-- Navbar Start -->
        @include('home.navbar', ['title' => 'Products'])
        <!-- Navbar End -->

        <!-- Sidebar Start -->
        <div class="border-r border-gray-300 h-full w-60 fixed mt-16">
            <div class="p-4">
                <h1 class="text-xl font-bold">Categories</h1>
                <ul class="mt-4">
                    <li class="mb-2">
                        <a href="{{ route('home.products') }}"
                            class="{{ Route::is('home.products') ? 'text-yellow-400' : 'text-gray-400 hover:text-yellow-400' }}">Products</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('home.reservations') }}"
                            class="text-gray-400 hover:text-yellow-400">Reservations</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="flex-1 p-4 pl-64 pt-20">
            <!-- Alert success -->
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif
            <!-- Akhir alert success -->

            <!-- Alert success -->
            @if ($message = Session::get('error'))
                <div class="alert alert-warning">
                    <p>{{ $message }}</p>
                </div>
            @endif
            <!-- Akhir alert success -->
            <div class="grid grid-cols-3 gap-4">
                @foreach ($barang as $b)
                    <div class="bg-white p-4 shadow-md">
                        <img src="{{ asset('assets/images/barang/' . $b->gambar_barang) }}"
                            alt="{{ $b->gambar_barang }}" class="w-full object-cover">
                        <h1 class="text-lg font-bold mt-2">{{ $b->nama_barang }}</h1>
                        <h1 class="text-base font-bold mt-2">{{ $b->stok }} Pcs</h1>
                        <p class="text-yellow-400 font-bold mt-2">Rp.
                            {{ number_format($b->harga_satuan, 0, ',', '.') }}
                        </p>
                        @if (Auth::check())
                            <a href="{{ route('home.add-cart', $b->id) }}"
                                class="bg-yellow-400 text-white px-4 py-2 mt-2 inline-block rounded-md">
                                <i class="fe-shopping-cart"></i>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
            <!-- Content End -->
        </div>
</body>

</html>
