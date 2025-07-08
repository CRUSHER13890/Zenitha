<x-app-layout :title="'Detail Pendapatan Jasa'">
    <div class="container-fluid">
        <!-- Alert messages -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-warning">
                <p>{{ $message }}</p>
            </div>
        @endif
        
        <div class="alert alert-success" id="diskon-alert" style="display: none;">
            <p id="diskon-message"></p>
        </div>
        
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <h1 class="h3 mb-2 text-gray-800 text-primary">Data {{ $title }}</h1>
                        </div>
                        <div class="float-end">
                            <a class="btn btn-primary waves-effect waves-light" href="{{ route('pendapatan.index') }}">
                                Kembali
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="sub-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-start mt-3">
                                    <p class="m-t-10"><strong>Nomor Pendapatan : </strong>{{ $pendapatan->no_pendapatan }}</p>
                                    <p class="m-t-10"><strong>Tanggal Pendapatan : </strong>{{ date('d F Y', strtotime($pendapatan->tgl_pendapatan)) }}</p>
                                </div>
                                <div class="float-end mt-3">
                                    <p class="m-t-10"><strong>Nama Pelanggan : </strong>{{ $pendapatan->nama_pelanggan }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('pendapatan.storedetail') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_pendapatan_header" value="{{ $pendapatan->id }}">
                        
                        <div class="mb-3">
                            <label for="id_reservasi" class="form-label">Reservasi</label>
                            <select class="form-select @error('id_reservasi') border border-danger @enderror" id="id_reservasi" name="id_reservasi">
                                <option value="" disabled selected> ==>> Pilih Reservasi <<== </option>
                                @foreach ($reservations as $reservation)
                                    <option value="{{ $reservation->id }}" 
                                        data-harga="{{ $reservation->harga }}"
                                        data-layanan="{{ $reservation->nama_layanan }}"
                                        data-id-layanan="{{ $reservation->id_layanan }}">
                                        {{ $reservation->no_reservasi }} - {{ $reservation->ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_reservasi')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="nama_layanan" class="form-label">Nama Layanan</label>
                            <input type="text" class="form-control" id="nama_layanan" readonly>
                            <input type="hidden" id="id_layanan" name="id_layanan">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="harga" class="form-label">Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control" id="harga" readonly>
                                    <input type="hidden" id="harga-raw" name="harga">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="diskon_display" class="form-label">Diskon</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <input type="text" class="form-control" id="diskon_persen" value="0" readonly>
                                    <input type="hidden" id="diskon-raw" name="diskon" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="subtotal_display" class="form-label">Subtotal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control bg-success text-white" id="subtotal_display" readonly>
                                    <input type="hidden" id="subtotal-raw" name="subtotal">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan</button>
                            <a class="btn btn-secondary waves-effect" href="{{ url('/pendapatan') }}">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered dt-responsive nowrap">
                        <thead>
                            <tr align="center">
                                <th>No Reservasi</th>
                                <th>Layanan</th>
                                <th>Harga</th>
                                <th>Diskon</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach ($pendapatan_detail as $p)
                                <tr>
                                    <td>{{ $p->no_reservasi }}</td>
                                    <td>{{ $p->nama_layanan }}</td>
                                    <td align="right">Rp. {{ number_format($p->harga, 0, ',', '.') }}</td>
                                    <td align="right">
                                        @if ($p->diskon > 0)
                                            <span class="text-success">{{ number_format(($p->diskon/$p->harga)*100, 0) }}%</span>
                                        @else
                                            <span class="text-muted">0%</span>
                                        @endif
                                    </td>
                                    <td align="right">Rp. {{ number_format($p->subtotal, 0, ',', '.') }}</td>
                                    <td>
                                        <button onclick="deleteConfirm(this)" data-id="{{ $p->id }}" class="btn btn-danger btn-sm">Hapus</button>
                                    </td>
                                </tr>
                                @php $total += $p->subtotal; @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"><strong>Total</strong></td>
                                <td align="right"><strong>Rp. {{ number_format($total, 0, ',', '.') }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteConfirm(e) {
            const id = e.getAttribute('data-id');
            const url = "{{ url('pendapatan/destroydetail/') }}/" + id;
            document.getElementById('btn-delete').setAttribute('href', url);
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectReservasi = document.getElementById('id_reservasi');
            const diskonPersen = document.getElementById('diskon_persen');
            
            // Format number
            const formatNumber = num => new Intl.NumberFormat('id-ID').format(num);
            const parseNumber = str => parseInt(str.replace(/\D/g, '')) || 0;
            
            // Calculate subtotal
            function calculateSubtotal() {
                const harga = parseNumber(document.getElementById('harga').value);
                const diskon = parseNumber(diskonPersen.value);
                const diskonAmount = harga * diskon / 100;
                const subtotal = harga - diskonAmount;
                
                document.getElementById('diskon-raw').value = diskonAmount;
                document.getElementById('subtotal_display').value = formatNumber(subtotal);
                document.getElementById('subtotal-raw').value = subtotal;
            }
            
            // When reservation selected
            selectReservasi.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const harga = selected.getAttribute('data-harga');
                const layanan = selected.getAttribute('data-layanan');
                const idLayanan = selected.getAttribute('data-id-layanan');
                
                document.getElementById('nama_layanan').value = layanan;
                document.getElementById('id_layanan').value = idLayanan;
                document.getElementById('harga').value = formatNumber(harga);
                document.getElementById('harga-raw').value = harga;
                
                // Reset diskon
                diskonPersen.value = '0';
                calculateSubtotal();
                
                // Check for available discounts
                if (idLayanan) {
                    fetch('{{ route('pendapatan.getDiskon') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id_layanan: idLayanan,
                            tgl_pendapatan: '{{ $pendapatan->tgl_pendapatan }}'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            diskonPersen.value = data.diskon.persentase_diskon;
                            calculateSubtotal();
                            
                            document.getElementById('diskon-message').innerHTML = 
                                `Diskon ${data.diskon.persentase_diskon}% (${data.diskon.nama_diskon}) berlaku`;
                            document.getElementById('diskon-alert').style.display = 'block';
                        }
                    });
                }
            });
            
            // When discount percentage changes
            diskonPersen.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                calculateSubtotal();
            });
        });
    </script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a id="btn-delete" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>