<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>History Reservations</title>
    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container-lg h-screen">
        <!-- Navbar Start -->
        @include('home.navbar', ['title' => 'History Reservations'])
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
                            class="{{ Route::is('home.history-reservation') ? 'text-yellow-400' : 'text-gray-400 hover:text-yellow-400' }}">Reservations</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="flex-1 p-4 pl-64 pt-20">
            <div class="grid grid-cols-1 gap-4">
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
                @foreach ($history as $b)
                    <div class="bg-white p-4 shadow-md flex justify-between items-center">
                        <div>
                            <h1 class="text-lg font-bold mt-2">{{ $b->no_reservasi }}</h1>
                            <h1 class="text-lg font-bold mt-2">{{ $b->nama_layanan }}</h1>
                            <p class="text-base font-bold mt-2">{{ date('d F Y', strtotime($b->tgl_reservasi)) }}</p>
                            <p class="text-base font-bold mt-2">{{ date('H:i', strtotime($b->waktu_mulai)) }}</p>
                            @if ($b->status === 'pending')
                                <p class="text-sm text-yellow-600 italic mt-2">
                                    Dimohon pelanggan menunggu hingga status berubah menjadi Disetujui, yang menandakan
                                    reservasi telah dikonfirmasi oleh pihak klinik dan pelanggan dapat langsung datang
                                    sesuai jadwal.
                                </p>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-lg font-bold mt-2">Status :
                                @if ($b->status === 'pending')
                                    -
                                @else
                                    {{ ucwords(str_replace('_', ' ', $b->status)) }}
                                @endif
                            </h1>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Content End -->
        </div>
</body>

<script>
    // Fungsi untuk memuat ulang data reservasi
    function loadReservationHistory() {
        fetch('{{ route('home.history-reservation') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.grid.grid-cols-1.gap-4').innerHTML;
                document.querySelector('.grid.grid-cols-1.gap-4').innerHTML = newContent;
            })
            .catch(error => console.error('Error:', error));
    }

    // Polling setiap 10 detik
    setInterval(loadReservationHistory, 10000);

    // Load pertama kali
    document.addEventListener('DOMContentLoaded', loadReservationHistory);
</script>

</html>
