<!-- dashboard -->
<x-app-layout>
    <div class="container-fluid">
        <h4 class="mb-4 fw-bold text-primary">Dashboard</h4>

        {{-- Grafik Bulanan --}}
        <div class="row mb-5">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3 text-black fw-bold">Grafik Bulanan Penjualan vs Pengeluaran ({{ $tahun }})
                        </h5>
                        <canvas id="chartBulanan" height="130"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bulan untuk Grafik Harian --}}
        <form method="GET" action="{{ route('dashboard') }}" class="row g-3 align-items-center mb-4">
            <input type="hidden" name="year" value="{{ $tahun }}">
            <div class="col-auto">
                <label for="month" class="col-form-label">Tampilkan Grafik Harian untuk Bulan:</label>
            </div>
            <div class="col-auto">
                <select name="month" id="month" class="form-select">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Tampilkan</button>
            </div>
        </form>

        {{-- Grafik Harian --}}
        <div class="row">
            <div class="col-xl-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3 text-black fw-bold">Grafik Harian Penjualan vs Pengeluaran
                            ({{ $bulan }}/{{ $tahun }})</h5>
                        <canvas id="chartHarian" height="130"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // === Grafik Bulanan ===
                const bulanan = <?= json_encode($result) ?>;
                const bulananLabels = bulanan.map(item => item.waktu);
                const bulananPenjualan = bulanan.map(item => item.total);
                const bulananPendapatan =
                <?= json_encode(array_map(fn($d) => $d->total, $pendapatan)) ?>; // Changed from pengeluaran to pendapatan

                new Chart(document.getElementById('chartBulanan'), {
                    type: 'bar',
                    data: {
                        labels: bulananLabels,
                        datasets: [{
                                label: 'Penjualan',
                                data: bulananPenjualan,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderRadius: 6
                            },
                            {
                                label: 'Pendapatan', 
                                data: bulananPendapatan,
                                backgroundColor: 'rgba(255, 89, 92)', 
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Data Penjualan dan Pendapatan per Bulan', 
                                font: {
                                    size: 18
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // === Grafik Harian ===
                const harianLabels = {!! json_encode(array_column($penjualanHarian, 'tanggal')) !!};
                const harianPenjualan = {!! json_encode(array_map(fn($d) => $d->total, $penjualanHarian)) !!};
                const harianPendapatan = {!! json_encode(array_map(fn($d) => $d->total, $pendapatanHarian)) !!}; // Changed from pengeluaranHarian to pendapatanHarian

                new Chart(document.getElementById('chartHarian'), {
                    type: 'line',
                    data: {
                        labels: harianLabels,
                        datasets: [{
                                label: 'Penjualan',
                                data: harianPenjualan,
                                borderColor: 'blue',
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Pendapatan',
                                data: harianPendapatan,
                                borderColor: 'red', 
                                backgroundColor: 'rgba(255, 99, 132, 0.1)', 
                                tension: 0.3,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Grafik Harian Penjualan & Pendapatan', 
                                font: {
                                    size: 18
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
