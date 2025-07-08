<x-app-layout :title="'Detail Penjualan'">
    <div class="container-fluid">
        <!-- Alert success -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <!-- Akhir alert success -->

        <!-- Alert error -->
        @if ($message = Session::get('error'))
            <div class="alert alert-warning">
                <p>{{ $message }}</p>
            </div>
        @endif
        <!-- Akhir alert error -->
        
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <h1 class="h3 mb-2 text-gray-800 text-primary">Data {{ $title }}</h1>
                        </div>
                        <div class="float-end">
                            <a class="btn btn-primary waves-effect waves-light" href="{{ route('penjualan.index') }}">
                                Kembali
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="sub-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-start mt-3">
                                    <p class="m-t-10"><strong>Nomor Penjualan : </strong> {{ $penjualan->no_penjualan }}</p>
                                    <p class="m-t-10"><strong>Tanggal Penjualan : </strong>
                                        {{ date('d F Y', strtotime($penjualan->tgl_penjualan)) }}
                                    </p>
                                </div>
                                <div class="float-end mt-3">
                                    <p class="m-t-10"><strong>Keterangan : </strong> {{ $penjualan->keterangan }} </p>
                                    <p class="m-t-10"><strong>Nama Pelanggan : </strong>
                                        {{ $penjualan->nama_pelanggan }} </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('penjualan.storedetail') }}" method="POST" id="form-penjualan">
                        @csrf
                        <input type="hidden" name="id_penjualan_header" value="{{ $penjualan->id }}">
                        <input type="hidden" name="no_penjualan" value="{{ $penjualan->no_penjualan }}">

                        <div class="mb-3">
                            <label for="id_barang" class="form-label">Barang</label>
                            <select class="form-select @error('id_barang') is-invalid @enderror" 
                                    id="id_barang" name="id_barang" required>
                                <option value="" disabled selected>Pilih Barang</option>
                                @foreach ($barang as $item)
                                    <option value="{{ $item->id_barang }}" 
                                            data-harga="{{ $item->harga_jual_satuan }}"
                                            data-stok="{{ $item->stok }}"
                                            data-persediaan="{{ $item->id_persediaan }}"
                                            data-tanggal="{{ $item->tgl_persediaan }}">
                                        {{ $item->nama_barang }} (Stok: {{ $item->stok }}) - FIFO: {{ date('d/m/Y', strtotime($item->tgl_persediaan)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Harga menggunakan metode FIFO (First In, First Out)</small>
                        </div>

                        <div class="mb-3">
                            <label for="kuantitas" class="form-label">Kuantitas</label>
                            <input type="number" class="form-control @error('kuantitas') is-invalid @enderror" 
                                   id="kuantitas" name="kuantitas" min="1" required
                                   oninput="calculateTotal()">
                            <small class="text-muted">
                                Stok tersedia: <span id="stok-tersedia">0</span> | 
                                FIFO dari: <span id="tanggal-fifo">-</span>
                            </small>
                            @error('kuantitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="harga_satuan" class="form-label">Harga Satuan (FIFO)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control @error('harga_satuan') is-invalid @enderror"
                                       id="harga_satuan" readonly>
                                <input type="hidden" name="harga_satuan" id="harga_satuan_raw">
                            </div>
                            <small class="text-muted">Harga otomatis sesuai urutan persediaan (FIFO)</small>
                            @error('harga_satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info" id="fifo-info" style="display: none;">
                            <p id="fifo-message"></p>
                        </div>

                        <div class="alert alert-success" id="diskon-alert" style="display: none;">
                            <p id="diskon-message"></p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="total" class="form-label">Total (Kuantitas × Harga FIFO)</label>
                                <input type="text" class="form-control" id="total" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="diskon_display" class="form-label">Diskon</label>
                                <input type="text" class="form-control" id="diskon_display" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="subtotal_display" class="form-label">Subtotal</label>
                                <input type="text" class="form-control bg-success text-white" id="subtotal_display"
                                       readonly>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary me-md-2">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="card">
                <div class="card-body table-responsive">
                    {{-- <div class="alert alert-info">
                        <strong>Info FIFO:</strong> Sistem menggunakan metode FIFO (First In, First Out) untuk pengambilan stok. 
                        Barang yang masuk pertama akan keluar/terjual terlebih dahulu.
                    </div> --}}
                    <table class="table table-bordered table-hover dt-responsive nowrap" id="responsive-datatable">
                        <thead class="table-light">
                            <tr align="center">
                                <th>Nama Barang</th>
                                <th>Kuantitas</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                                <th>Diskon</th>
                                <th>Subtotal</th>
                                {{-- <th>Metode</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_keseluruhan = 0;
                            @endphp
                            @foreach ($penjualan_detail as $p)
                                <tr>
                                    <td>{{ $p->nama_barang }}</td>
                                    <td align="center">{{ number_format($p->kuantitas, 0) }}</td>
                                    <td align="right">Rp. {{ number_format($p->harga_satuan, 0, ',', '.') }}</td>
                                    <td align="right">Rp. {{ number_format($p->harga_satuan * $p->kuantitas, 0, ',', '.') }}</td>
                                    <td align="right">
                                        @if ($p->diskon > 0)
                                            <span class="text-success">-Rp. {{ number_format($p->diskon, 0, ',', '.') }}</span>
                                            @if ($p->nama_diskon)
                                                <br><small class="text-muted">({{ $p->nama_diskon }})</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td align="right" class="fw-bold">
                                        Rp. {{ number_format($p->subtotal, 0, ',', '.') }}
                                    </td>
                                    {{-- <td align="center">
                                        <span class="badge bg-primary">FIFO</span>
                                    </td> --}}
                                    <td align="center">
                                        <button onclick="deleteConfirm(this)" 
                                                data-id="{{ $p->id }}"
                                                data-barang="{{ $p->nama_barang }} - {{ $p->kuantitas }} - Rp. {{ number_format($p->harga_satuan, 0, ',', '.') }}"
                                                class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @php
                                    $total_keseluruhan += $p->subtotal;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Total Pembelian</td>
                                <td align="right" class="fw-bold">Rp. {{ number_format($total_keseluruhan, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="deleteModalBody">
                    <!-- Content will be inserted by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a id="btn-delete" class="btn btn-danger" href="#">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Format number with ID locale
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set up barang selection
            const barangSelect = document.getElementById('id_barang');
            barangSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const harga = selectedOption.getAttribute('data-harga');
                const stok = selectedOption.getAttribute('data-stok');
                const tanggal = selectedOption.getAttribute('data-tanggal');

                // Update harga display
                document.getElementById('harga_satuan').value = formatRupiah(harga);
                document.getElementById('harga_satuan_raw').value = harga;
                
                // Update stok info
                document.getElementById('stok-tersedia').textContent = stok;
                document.getElementById('tanggal-fifo').textContent = formatTanggal(tanggal);
                document.getElementById('kuantitas').max = stok;
                document.getElementById('kuantitas').value = '';
                
                // Show FIFO info
                showFifoInfo(harga, tanggal);
                
                // Reset calculations
                resetCalculations();
            });
        });

        // Format tanggal
        function formatTanggal(tanggal) {
            const date = new Date(tanggal);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit', 
                year: 'numeric'
            });
        }

        // Show FIFO information
        function showFifoInfo(harga, tanggal) {
            const fifoAlert = document.getElementById('fifo-info');
            const fifoMessage = document.getElementById('fifo-message');
            
            fifoAlert.style.display = 'block';
            fifoMessage.innerHTML = 
                `<i class="fas fa-info-circle"></i> <strong>FIFO Info:</strong> 
                Menggunakan harga <strong>Rp. ${formatRupiah(harga)}</strong> 
                dari persediaan tanggal <strong>${formatTanggal(tanggal)}</strong> (persediaan tertua).
                <button type="button" class="btn-close float-end" aria-label="Close" onclick="document.getElementById('fifo-info').style.display='none'"></button>`;
        }

        // Calculate totals
        function calculateTotal() {
            const kuantitas = parseInt(document.getElementById('kuantitas').value) || 0;
            const harga = parseInt(document.getElementById('harga_satuan_raw').value) || 0;
            const total = kuantitas * harga;

            // Update displays
            document.getElementById('total').value = 'Rp. ' + formatRupiah(total);
            document.getElementById('subtotal_display').value = 'Rp. ' + formatRupiah(total);

            // Check for diskon if barang is selected and quantity > 0
            const barangId = document.getElementById('id_barang').value;
            if (kuantitas > 0 && barangId) {
                checkDiskon(barangId, total);
            } else {
                hideDiskon();
                document.getElementById('diskon_display').value = 'Rp. 0';
                document.getElementById('subtotal_display').value = 'Rp. ' + formatRupiah(total);
            }
        }

        // Reset all calculations
        function resetCalculations() {
            document.getElementById('total').value = '';
            document.getElementById('diskon_display').value = '';
            document.getElementById('subtotal_display').value = '';
            hideDiskon();
        }

        // Hide diskon alert
        function hideDiskon() {
            document.getElementById('diskon-alert').style.display = 'none';
        }

        // Check for diskon via AJAX
        function checkDiskon(barangId, total) {
            fetch('{{ route("penjualan.getDiskon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_barang: barangId,
                    total: total,
                    tgl_penjualan: '{{ $penjualan->tgl_penjualan }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                const diskonAlert = document.getElementById('diskon-alert');
                const diskonMessage = document.getElementById('diskon-message');
                const diskonDisplay = document.getElementById('diskon_display');
                const subtotalDisplay = document.getElementById('subtotal_display');

                if (data.success) {
                    // Show diskon
                    diskonAlert.style.display = 'block';
                    diskonMessage.innerHTML = 
                        `<i class="fas fa-gift"></i> Selamat! Anda mendapat diskon <strong>${data.diskon.nama_diskon}</strong> sebesar <strong>Rp. ${formatRupiah(data.diskon_nominal)}</strong> 
                        <button type="button" class="btn-close float-end" aria-label="Close" onclick="document.getElementById('diskon-alert').style.display='none'"></button>`;
                    
                    diskonDisplay.value = 'Rp. ' + formatRupiah(data.diskon_nominal);
                    subtotalDisplay.value = 'Rp. ' + formatRupiah(data.subtotal);
                } else {
                    // Hide diskon if none available
                    hideDiskon();
                    diskonDisplay.value = 'Rp. 0';
                    subtotalDisplay.value = 'Rp. ' + formatRupiah(total);
                }
            })
            .catch(error => {
                console.error('Error checking diskon:', error);
                hideDiskon();
                document.getElementById('diskon_display').value = 'Rp. 0';
                document.getElementById('subtotal_display').value = 'Rp. ' + formatRupiah(total);
            });
        }

        // Delete confirmation
        function deleteConfirm(button) {
            const id = button.getAttribute('data-id');
            const barang = button.getAttribute('data-barang');
            const url = "{{ url('penjualan/destroydetail') }}/" + id;

            document.getElementById('deleteModalBody').innerHTML = 
                `Apakah Anda yakin ingin menghapus item penjualan:<br><strong>${barang}</strong>?<br><br>
                <small class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Menghapus item ini akan mengembalikan stok ke persediaan berdasarkan metode FIFO.
                </small>`;
            document.getElementById('btn-delete').setAttribute('href', url);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
</x-app-layout>