
<!DOCTYPE html>
<html lang="en">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container-lg h-screen overflow-hidden">
        <!-- Navbar Start -->
        @include('home.navbar', ['title' => 'Cart'])
        <!-- Navbar End -->

        <!-- Content Start -->
        <div
            class="flex-1 p-4 pt-20 {{ Session::has('cart') && count(Session::get('cart')) > 0 ? 'w-3/4' : 'w-full' }}">
            @if (Session::has('cart') && count(Session::get('cart')) > 0)
                <a href="{{ route('home.products') }}"
                    class="px-5 py-2 border bg-gray-700 text-center text-white rounded-md ms-2">Kembali</a>
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
                <div class="grid grid-cols-3 gap-4 mt-10">
                    @foreach (Session::get('cart') as $id => $c)
                        <div class="bg-white p-4 shadow-md">
                            <img src="{{ asset('assets/images/barang/' . $c['gambar_barang']) }}"
                                alt="{{ $c['gambar_barang'] }}" class="w-full object-cover">
                            <div class="flex justify-between">
                                <div>
                                    <h1 class="text-lg font-bold mt-2">{{ $c['nama_barang'] }}</h1>
                                    <h1 class="text-base font-bold mt-2">{{ $c['stok'] }} Pcs</h1>
                                    <p class="text-yellow-400 font-bold mt-2">Rp.
                                        {{ number_format($c['harga_satuan'], 0, ',', '.') }}
                                    </p>
                                    @if(isset($c['diskon']) && $c['diskon'])
                                        <div class="bg-green-100 border border-green-400 text-green-700 px-2 py-1 mt-2 rounded">
                                            <p class="text-sm">Diskon: {{ $c['diskon']->nama_diskon }} ({{ $c['diskon']->persentase_diskon }}%)</p>
                                            <p class="text-sm">Hemat: Rp. {{ number_format($c['diskon_nominal'], 0, ',', '.') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex justify-center items-center">
                                    <a href="{{ $c['stok'] != 0 ? route('home.increment-cart', $id) : 'javascript:void(0)' }}"
                                        class="border bg-white py-1 px-2 rounded-md">+</a>
                                    <p class="ms-2">{{ $c['kuantitas'] }}</p>
                                    <a href="{{ route('home.decrement-cart', $id) }}"
                                        class="border bg-white py-1 px-2 rounded-md ms-2">-</a>
                                </div>
                            </div>
                            <a href="{{ route('home.remove-cart', $id) }}"
                                class="text-white px-4 py-2 mt-2 inline-block rounded-md"
                                style="background-color: #f87171">
                                <i class="fe-trash-2"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center h-full flex-col">
                    <h1 class="text-6xl font-bold text-yellow-400">Cart is Empty</h1>
                    <a href="{{ route('home.products') }}"
                        class="border-2 border-gray-500 rounded-md bg-yellow-400 px-5 py-2 mt-2 hover:bg-yellow-500">Explore</a>
                </div>
            @endif

            <!-- Content End -->

            <!-- Right Bar Start -->
            @if (Session::has('cart') && count(Session::get('cart')) > 0)
                <div class="border-l border-gray-300 h-screen fixed mt-16 top-0" style="right: 0px; width: 300px;">
                    <div class="p-4 h-full">
                        <h1 class="text-xl font-bold">Detail</h1>
                        <div>
                            <ul>
                                @php
                                    $total = 0;
                                    $total_diskon = 0;
                                    $grand_total = 0;
                                @endphp
                                @foreach (Session::get('cart') as $id => $c)
                                    <li class="flex justify-between items-center mt-4">
                                        <div>
                                            <h1 class="text-lg font-bold">{{ $c['nama_barang'] }}</h1>
                                            <p class="text-yellow-400 font-bold mt-2">Rp.
                                                {{ number_format($c['harga_satuan'], 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <p>{{ $c['kuantitas'] }} Pcs</p>
                                    </li>
                                    @php
                                        $item_total = $c['harga_satuan'] * $c['kuantitas'];
                                        $total += $item_total;
                                        $item_diskon = isset($c['diskon_nominal']) ? $c['diskon_nominal'] : 0;
                                        $total_diskon += $item_diskon;
                                        $grand_total += isset($c['subtotal']) ? $c['subtotal'] : $item_total;
                                    @endphp
                                @endforeach
                            </ul>
                        </div>
                        <div class="flex justify-between items-center">
                            <h1 class="text-lg font-bold mt-4">Total</h1>
                            <p class="text-gray-600 mt-2">Rp. {{ number_format($total, 0, ',', '.') }}</p>
                        </div>
                        @if($total_diskon > 0)
                        <div class="flex justify-between items-center">
                            <h1 class="text-lg font-bold mt-2">Diskon</h1>
                            <p class="text-green-600 mt-2">- Rp. {{ number_format($total_diskon, 0, ',', '.') }}</p>
                        </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <h1 class="text-lg font-bold mt-2">Subtotal</h1>
                            <p class="text-yellow-400 font-bold mt-2">Rp. {{ number_format($grand_total, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <div class="mt-4 text-center">
                                <a href="{{ route('home.checkout') }}"
                                    class="bg-yellow-400 text-white px-4 py-2 mt-2 inline-block">Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Right Bar End -->
        </div>
    </div>
</body>

</html>