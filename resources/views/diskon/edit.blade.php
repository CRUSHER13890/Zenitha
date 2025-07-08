<x-app-layout :title="'Edit Diskon'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('diskon.update', $diskon->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="kode_diskon" class="form-label">Kode Diskon</label>
                                <input type="text"
                                    class="form-control @error('kode_diskon') border border-danger @enderror"
                                    id="kode_diskon" name="kode_diskon" value="{{ $diskon->kode_diskon }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="nama_diskon" class="form-label">Nama Diskon</label>
                                <input type="text"
                                    class="form-control @error('nama_diskon') border border-danger @enderror"
                                    id="nama_diskon" name="nama_diskon" placeholder="Contoh : Diskon Akhir Tahun"
                                    value="{{ $diskon->nama_diskon }}" />
                                @if ($errors->has('nama_diskon'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_diskon') }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <label for="tipe_diskon" class="form-label">Tipe Diskon</label>
                                <select class="form-select @error('tipe_diskon') border border-danger @enderror"
                                    id="tipe_diskon" name="tipe_diskon">
                                    <option value="" disabled> ==>> Pilih Tipe Diskon <<== </option>
                                    <option value="barang" {{ $tipe_diskon == 'barang' ? 'selected' : '' }}>Barang</option>
                                    <option value="layanan" {{ $tipe_diskon == 'layanan' ? 'selected' : '' }}>Layanan</option>
                                </select>
                                @if ($errors->has('tipe_diskon'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tipe_diskon') }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <label for="persentase_diskon_display" class="form-label">Persentase Diskon</label>
                                <div class="input-group">
                                    <input type="text" inputmode="numeric"
                                        class="form-control @error('persentase_diskon') border border-danger @enderror"
                                        id="persentase_diskon_display" placeholder="Contoh : 10"
                                        value="{{ $diskon->persentase_diskon }}" />
                                    <span class="input-group-text">%</span>
                                </div>
                                <input type="hidden" name="persentase_diskon" id="persentase_diskon"
                                    value="{{ $diskon->persentase_diskon }}" />
                                @if ($errors->has('persentase_diskon'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('persentase_diskon') }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Fields for Barang -->
                            <div id="barang-fields" style="{{ $tipe_diskon == 'barang' ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label for="min_transaksi_display" class="form-label">Minimal Transaksi</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" inputmode="numeric"
                                            class="form-control @error('min_transaksi') border border-danger @enderror"
                                            id="min_transaksi_display" placeholder="Contoh : 100.000"
                                            value="{{ number_format($diskon->min_transaksi, 0, ',', '.') }}" />
                                        <input type="hidden" name="min_transaksi" id="min_transaksi"
                                            value="{{ $diskon->min_transaksi }}" />
                                    </div>
                                    @if ($errors->has('min_transaksi'))
                                        <div class="fw-light fs-6 mt-1 text-danger">
                                            {{ $errors->first('min_transaksi') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="max_diskon_display" class="form-label">Maksimal Diskon</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" inputmode="numeric"
                                            class="form-control @error('max_diskon') border border-danger @enderror"
                                            id="max_diskon_display" placeholder="Contoh : 50.000"
                                            value="{{ number_format($diskon->max_diskon, 0, ',', '.') }}" />
                                        <input type="hidden" name="max_diskon" id="max_diskon"
                                            value="{{ $diskon->max_diskon }}" />
                                    </div>
                                    @if ($errors->has('max_diskon'))
                                        <div class="fw-light fs-6 mt-1 text-danger">
                                            {{ $errors->first('max_diskon') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="id_barang" class="form-label">Barang</label>
                                    <select class="form-select @error('id_barang') border border-danger @enderror"
                                        id="id_barang" name="id_barang">
                                        <option value="" disabled> ==>> Pilih Barang <<== </option>
                                        @foreach ($barang as $b)
                                        <option value="{{ $b->id }}"
                                            {{ $diskon->id_barang == $b->id ? 'selected' : '' }}>{{ $b->nama_barang }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_barang'))
                                        <div class="fw-light fs-6 mt-1 text-danger">
                                            {{ $errors->first('id_barang') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Fields for Layanan -->
                            <div id="layanan-fields" style="{{ $tipe_diskon == 'layanan' ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label for="id_layanan" class="form-label">Layanan</label>
                                    <select class="form-select @error('id_layanan') border border-danger @enderror"
                                        id="id_layanan" name="id_layanan">
                                        <option value="" disabled> ==>> Pilih Layanan <<== </option>
                                        @foreach ($layanan as $l)
                                        <option value="{{ $l->id }}"
                                            {{ $diskon->id_layanan == $l->id ? 'selected' : '' }}>{{ $l->nama_layanan }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_layanan'))
                                        <div class="fw-light fs-6 mt-1 text-danger">
                                            {{ $errors->first('id_layanan') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Date Range Fields -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="use_date_range" name="use_date_range" 
                                        {{ isset($use_date_range) && $use_date_range ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_date_range">
                                        Gunakan Periode Diskon
                                    </label>
                                </div>
                            </div>
                            
                            <div id="date-range-fields" style="{{ isset($use_date_range) && $use_date_range ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date"
                                        class="form-control @error('tanggal_mulai') border border-danger @enderror"
                                        id="tanggal_mulai" name="tanggal_mulai" 
                                        value="{{ $diskon->tanggal_mulai ? date('Y-m-d', strtotime($diskon->tanggal_mulai)) : '' }}" />
                                    @if ($errors->has('tanggal_mulai'))
                                        <div class="fw-light fs-6 mt-1 text-danger">
                                            {{ $errors->first('tanggal_mulai') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                    <input type="date"
                                        class="form-control @error('tanggal_selesai') border border-danger @enderror"
                                        id="tanggal_selesai" name="tanggal_selesai" 
                                        value="{{ $diskon->tanggal_selesai ? date('Y-m-d', strtotime($diskon->tanggal_selesai)) : '' }}" />
                                    @if ($errors->has('tanggal_selesai'))
                                        <div class="fw-light fs-6 mt-1 text-danger">
                                            {{ $errors->first('tanggal_selesai') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <input type="submit" class="btn btn-primary" value="Update" />
                            <a href="{{ route('diskon.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
    @push('scripts')
        <script>
            // Format currency inputs
            document.getElementById('min_transaksi_display').addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                document.getElementById('min_transaksi').value = value;
                this.value = formatRupiah(value);
            });

            document.getElementById('max_diskon_display').addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                document.getElementById('max_diskon').value = value;
                this.value = formatRupiah(value);
            });

            // Format percentage input
            document.getElementById('persentase_diskon_display').addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (parseInt(value) > 100) {
                    value = '100';
                }
                document.getElementById('persentase_diskon').value = value;
                this.value = value;
            });

            // Toggle fields based on discount type
            document.getElementById('tipe_diskon').addEventListener('change', function() {
                if (this.value === 'barang') {
                    document.getElementById('barang-fields').style.display = '';
                    document.getElementById('layanan-fields').style.display = 'none';
                } else {
                    document.getElementById('barang-fields').style.display = 'none';
                    document.getElementById('layanan-fields').style.display = '';
                }
            });

            // Toggle date range fields
            document.getElementById('use_date_range').addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById('date-range-fields').style.display = '';
                } else {
                    document.getElementById('date-range-fields').style.display = 'none';
                }
            });

            // Helper function to format currency
            function formatRupiah(angka) {
                var number_string = angka.toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah;
            }
        </script>
    @endpush
</x-app-layout>