<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Reservations</title>
    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container-lg h-screen">
        <!-- Navbar Start -->
        @include('home.navbar', ['title' => 'Add Reservations'])
        <!-- Navbar End -->

        <!-- Sidebar Start -->
        <div class="border-r border-gray-300 h-full w-60 fixed mt-16">
            <div class="p-4">
                <h1 class="text-xl font-bold">Categories</h1>
                <ul class="mt-4">
                    <li class="mb-2">
                        <a href="{{ route('home.products') }}" class="'text-gray-400 hover:text-yellow-400">Products</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('home.reservations') }}"
                            class="{{ Route::is('home.add-reservasi') ? 'text-yellow-400' : 'text-gray-400 hover:text-yellow-400' }}">Reservations</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="flex-1 p-4 pl-64 pt-20">
            <div class="grid grid-cols-1 gap-4">
                <div class="bg-white p-4 shadow-md">
                    <h1 class="text-lg font-bold mt-2">{{ $layanan->nama_layanan }}</h1>
                    <p class="text-yellow-400 font-bold mt-2">Rp.
                        {{ number_format($layanan->harga_layanan, 0, ',', '.') }}
                    </p>
                    <form action="{{ route('home.store-reservasi') }}" method="POST">
                        @csrf
                        <div class="mt-4">
                            <label for="no_reservasi">Nomor Reservasi</label>
                            <input type="text" name="no_reservasi" id="no_reservasi"
                                class="border border-gray-300 rounded-md w-full" value="{{ $no_reservasi }}" readonly>
                        </div>
                        <div class="mt-4">
                            <label for="tgl_reservasi">Tanggal Reservasi</label>
                            <input type="date" name="tgl_reservasi" id="tgl_reservasi"
                                class="border border-gray-300 rounded-md w-full" required
                                onclick="this.showPicker()">
                            @if ($errors->has('tgl_reservasi'))
                                <div class="font-light text-sm mt-1 text-red-500">
                                    {{ $errors->first('tgl_reservasi') }}
                                </div>
                            @endif
                        </div>
                        <div class="mt-4">
                            <label for="waktu_mulai">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" id="waktu_mulai"
                                class="border border-gray-300 rounded-md w-full" required
                                onclick="this.showPicker()">
                            @if ($errors->has('waktu_mulai'))
                                <div class="font-light text-sm mt-1 text-red-500">
                                    {{ $errors->first('waktu_mulai') }}
                                </div>
                            @endif
                        </div>
                        {{-- <div class="mt-4">
                            <label for="waktu_selesai">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" id="waktu_selesai"
                                class="border border-gray-300 rounded-md w-full">
                            @if ($errors->has('waktu_selesai'))
                                <div class="font-light text-sm mt-1 text-red-500">
                                    {{ $errors->first('waktu_selesai') }}
                                </div>
                            @endif
                        </div> --}}
                        <div class="mt-4">
                            <label for="nama_layanan">Nama Layanan</label>
                            <input type="text" name="nama_layanan" id="nama_layanan"
                                class="border border-gray-300 rounded-md w-full" value="{{ $layanan->nama_layanan }}"
                                readonly>
                            <!-- Hidden input to pass the layanan ID -->
                            <input type="hidden" name="id_layanan" value="{{ $layanan->id }}">
                        </div>
                        <button type="submit"
                            class="bg-yellow-400 text-white px-4 py-2 mt-4 inline-block rounded-md">Reservasi</button>
                    </form>
                </div>
            </div>
            <!-- Content End -->
        </div>
</body>

</html>
