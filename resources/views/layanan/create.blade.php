<x-app-layout :title="'Tambah Layanan'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('layanan.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="kode_layanan" class="form-label">Kode Layanan</label>
                                <input type="text"
                                    class="form-control @error('kode_layanan') border border-danger @enderror"
                                    id="kode_layanan" name="kode_layanan" value="{{ $kode_layanan }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="nama_layanan" class="form-label">Nama Layanan</label>
                                <input type="text"
                                    class="form-control @error('nama_layanan') border border-danger @enderror"
                                    id="nama_layanan" name="nama_layanan" placeholder="Contoh : Facial Treatment"
                                    value="{{ old('nama_layanan') }}" />
                                @if ($errors->has('nama_layanan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_layanan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="harga_layanan" class="form-label">Harga Layanan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" inputmode="numeric"
                                        class="form-control @error('harga_layanan') border border-danger @enderror"
                                        id="harga_layanan" name="harga_layanan" placeholder="Contoh : 10000"
                                        value="{{ old('harga_layanan') }}" />
                                </div>
                                @if ($errors->has('harga_layanan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('harga_layanan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Layanan</label>
                                <textarea class="form-control @error('deskripsi') border border-danger @enderror" id="deskripsi" name="deskripsi"
                                    placeholder="Contoh : Facial dengan menggunakan bahan alami yang aman untuk kulit." rows="3">{{ old('deskripsi') }}</textarea>
                                @if ($errors->has('deskripsi'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('deskripsi') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('layanan.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
    @push('scripts')
        <script>
            document.getElementById('harga_layanan').addEventListener('input', function() {
                const input = this.value.replace(/[^0-9]/g, '');
                const formatted = input.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.value = formatted;

                // Remove thousand separator before sending the value to the server
                const valueToSend = input.replace(/\./g, '');
                document.getElementById('harga_layanan').dataset.valueToSend = valueToSend;
            });

            // Submit the form with the formatted value
            document.querySelector('form').addEventListener('submit', function(event) {
                const input = document.getElementById('harga_layanan');
                input.value = input.dataset.valueToSend;
            });
        </script>
    @endpush
</x-app-layout>
