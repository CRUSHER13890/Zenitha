<x-app-layout :title="'Tambah Diskon'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('diskon.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="kode_diskon" class="form-label">Kode Diskon</label>
                                <input type="text"
                                    class="form-control @error('kode_diskon') border border-danger @enderror"
                                    id="kode_diskon" name="kode_diskon" value="{{ $kode_diskon }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="nama_diskon" class="form-label">Nama Diskon</label>
                                <input type="text"
                                    class="form-control @error('nama_diskon') border border-danger @enderror"
                                    id="nama_diskon" name="nama_diskon" placeholder="Contoh : Diskon Akhir Tahun"
                                    value="{{ old('nama_diskon') }}" />
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
                                    <option value="" disabled selected> ==>> Pilih Tipe Diskon <<== </option>
                                    <option value="barang" {{ old('tipe_diskon') == 'barang' ? 'selected' : '' }}>Barang</option>
                                    <option value="layanan" {{ old('tipe_diskon') == 'layanan' ? 'selected' : '' }}>Layanan</option>
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
                                        value="{{ old('persentase_diskon_display') }}" />
                                    <span class="input-group-text">%</span>
                                </div>
                                <input type="hidden" name="persentase_diskon" id="persentase_diskon"
                                    value="{{ old('persentase_diskon') }}" />
                                @if ($errors->has('persentase_diskon'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('persentase_diskon') }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Date Range Fields -->
                            <div class="mb-3">
                                <label for="tanggal_range" class="form-label">Periode Diskon</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="use_date_range" name="use_date_range">
                                    <label class="form-check-label" for="use_date_range">
                                        Aktifkan periode diskon
                                    </label>
                                </div>
                                <div id="date_range_container" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                                <input type="date" 
                                                    class="form-control @error('tanggal_mulai') border border-danger @enderror"
                                                    id="tanggal_mulai" name="tanggal_mulai" 
                                                    value="{{ old('tanggal_mulai') }}" />
                                                @if ($errors->has('tanggal_mulai'))
                                                    <div class="fw-light fs-6 mt-1 text-danger">
                                                        {{ $errors->first('tanggal_mulai') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                                <input type="date" 
                                                    class="form-control @error('tanggal_selesai') border border-danger @enderror"
                                                    id="tanggal_selesai" name="tanggal_selesai" 
                                                    value="{{ old('tanggal_selesai') }}" />
                                                @if ($errors->has('tanggal_selesai'))
                                                    <div class="fw-light fs-6 mt-1 text-danger">
                                                        {{ $errors->first('tanggal_selesai') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Fields for Barang -->
                            <div id="barang-fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="min_transaksi_display" class="form-label">Minimal Transaksi</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" inputmode="numeric"
                                            class="form-control @error('min_transaksi') border border-danger @enderror"
                                            id="min_transaksi_display" placeholder="Contoh : 100.000"
                                            value="{{ old('min_transaksi_display') }}" />
                                        <input type="hidden" name="min_transaksi" id="min_transaksi"
                                            value="{{ old('min_transaksi') }}" />
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
                                            value="{{ old('max_diskon_display') }}" />
                                        <input type="hidden" name="max_diskon" id="max_diskon"
                                            value="{{ old('max_diskon') }}" />
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
                                        <option value="" disabled selected> ==>> Pilih Barang <<== </option>
                                        @foreach ($barang as $b)
                                        <option value="{{ $b->id }}"
                                            {{ old('id_barang') == $b->id ? 'selected' : '' }}>{{ $b->nama_barang }}
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
                            <div id="layanan-fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="id_layanan" class="form-label">Layanan</label>
                                    <select class="form-select @error('id_layanan') border border-danger @enderror"
                                        id="id_layanan" name="id_layanan">
                                        <option value="" disabled selected> ==>> Pilih Layanan <<== </option>
                                        @foreach ($layanan as $l)
                                        <option value="{{ $l->id }}"
                                            {{ old('id_layanan') == $l->id ? 'selected' : '' }}>{{ $l->nama_layanan }}
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
                            
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('diskon.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->

    @push('scripts')
        <script>
            // Format angka dengan titik sebagai pemisah ribuan
            function formatNumber(number) {
                return number.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Menghapus format dan mengembalikan angka saja
            function unformatNumber(formattedNumber) {
                return formattedNumber.toString().replace(/\./g, "");
            }

            // Minimal Transaksi
            document.getElementById('min_transaksi_display').addEventListener('input', function(e) {
                let value = unformatNumber(this.value);
                this.value = formatNumber(value);
                document.getElementById('min_transaksi').value = value;
            });

            // Maksimal Diskon
            document.getElementById('max_diskon_display').addEventListener('input', function(e) {
                let value = unformatNumber(this.value);
                this.value = formatNumber(value);
                document.getElementById('max_diskon').value = value;
            });

            // Persentase Diskon
            document.getElementById('persentase_diskon_display').addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, "");
                if (value > 100) value = 100;
                this.value = value;
                document.getElementById('persentase_diskon').value = value;
            });

            // Toggle fields based on discount type
            document.getElementById('tipe_diskon').addEventListener('change', function() {
                const barangFields = document.getElementById('barang-fields');
                const layananFields = document.getElementById('layanan-fields');
                
                if (this.value === 'barang') {
                    barangFields.style.display = 'block';
                    layananFields.style.display = 'none';
                } else if (this.value === 'layanan') {
                    barangFields.style.display = 'none';
                    layananFields.style.display = 'block';
                } else {
                    barangFields.style.display = 'none';
                    layananFields.style.display = 'none';
                }
            });

            // Initialize fields based on selected type (for form validation errors)
            window.addEventListener('load', function() {
                const tipeDiskon = document.getElementById('tipe_diskon');
                if (tipeDiskon.value) {
                    tipeDiskon.dispatchEvent(new Event('change'));
                }
            });

            // Show/Hide date range fields based on checkbox
            document.getElementById('use_date_range').addEventListener('change', function() {
                const dateRangeContainer = document.getElementById('date_range_container');
                if (this.checked) {
                    dateRangeContainer.style.display = 'block';
                } else {
                    dateRangeContainer.style.display = 'none';
                }
            });

            // Initialize date range fields visibility based on checkbox state
            window.addEventListener('load', function() {
                const useDateRangeCheckbox = document.getElementById('use_date_range');
                if (useDateRangeCheckbox.checked) {
                    document.getElementById('date_range_container').style.display = 'block';
                }
            });

            // Initialize date range fields if they have values (for form validation errors)
            window.addEventListener('load', function() {
                const tanggalMulai = document.getElementById('tanggal_mulai');
                const tanggalSelesai = document.getElementById('tanggal_selesai');
                
                if (tanggalMulai.value || tanggalSelesai.value) {
                    document.getElementById('use_date_range').checked = true;
                    document.getElementById('date_range_container').style.display = 'block';
                }
            });

            // Submit form
            document.querySelector('form').addEventListener('submit', function() {
                document.getElementById('persentase_diskon').value = document.getElementById(
                    'persentase_diskon_display').value;
                
                // Only process these fields if barang is selected
                if (document.getElementById('tipe_diskon').value === 'barang') {
                    document.getElementById('min_transaksi').value = unformatNumber(document.getElementById(
                        'min_transaksi_display').value);
                    document.getElementById('max_diskon').value = unformatNumber(document.getElementById(
                        'max_diskon_display').value);
                }
                
                // If date range is not used, clear the date fields
                if (!document.getElementById('use_date_range').checked) {
                    document.getElementById('tanggal_mulai').value = '';
                    document.getElementById('tanggal_selesai').value = '';
                }
            });
        </script>
    @endpush
</x-app-layout>