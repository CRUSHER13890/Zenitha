<x-app-layout :title="$title">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">{{ $title }}</h5>

                <div class="row mb-3">
                    <div class="col-sm-3">Pilih Periode</div>
                    <div class="col-sm-9">
                        <input type="month" class="form-control" id="periode" onchange="loadFIFO()">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2">Tanggal</th>
                                <th colspan="3" class="text-cente">Pembelian</th>
                                <th colspan="3" class="text-center">HPP</th>
                                <th colspan="3" class="text-center">Persediaan</th>
                            </tr>
                            <tr>
                                <th>Unit</th><th>Harga/Unit</th><th>Total</th>
                                <th>Unit</th><th>Harga/Unit</th><th>Total</th>
                                <th>Unit</th><th>Harga/Unit</th><th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="laporan-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const today = new Date();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            document.getElementById('periode').value = `${year}-${month}`;
            loadFIFO();
        });

        function loadFIFO() {
            const periode = document.getElementById('periode').value;
            fetch(`/laporan/viewdatalaporanfifo/${periode}`)
                .then(res => res.json())
                .then(res => {
                    if (res.status === 200) {
                        const grouped = {};
                        res.data.forEach(row => {
                            if (!grouped[row.tanggal]) grouped[row.tanggal] = { pembelian: [], hpp: [], persediaan: [] };
                            grouped[row.tanggal][row.jenis].push(row);
                        });

                        const tbody = document.getElementById('laporan-body');
                        tbody.innerHTML = '';

                        Object.entries(grouped).forEach(([tgl, kategori]) => {
                            const max = Math.max(kategori.pembelian.length, kategori.hpp.length, kategori.persediaan.length);
                            for (let i = 0; i < max; i++) {
                                const pembelian = kategori.pembelian[i] || {};
                                const hpp = kategori.hpp[i] || {};
                                const persediaan = kategori.persediaan[i] || {};

                                tbody.innerHTML += `
                                    <tr>
                                        ${i === 0 ? `<td rowspan="${max}">${tgl}</td>` : ''}
                                        <td>${pembelian.unit ?? ''}</td>
                                        <td>${formatRupiah(pembelian.harga_per_unit)}</td>
                                        <td>${formatRupiah(pembelian.total)}</td>

                                        <td>${hpp.unit ?? ''}</td>
                                        <td>${formatRupiah(hpp.harga_per_unit)}</td>
                                        <td>${formatRupiah(hpp.total)}</td>

                                        <td>${persediaan.unit ?? ''}</td>
                                        <td>${formatRupiah(persediaan.harga_per_unit)}</td>
                                        <td>${formatRupiah(persediaan.total)}</td>
                                    </tr>`;
                            }
                        });
                    } else {
                        document.getElementById('laporan-body').innerHTML = `<tr><td colspan="10" class="text-center">${res.message}</td></tr>`;
                    }
                });
        }

        function formatRupiah(nilai) {
            if (nilai == null) return '';
            return 'Rp ' + Number(nilai).toLocaleString('id-ID');
        }
    </script>
</x-app-layout>
