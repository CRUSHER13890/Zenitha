<x-app-layout :title="'Tambah Pelanggan'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('pelanggan.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="kode_pelanggan" class="form-label">Kode Pelanggan</label>
                                <input type="text"
                                    class="form-control @error('kode_pelanggan') border border-danger @enderror"
                                    id="kode_pelanggan" name="kode_pelanggan" value="{{ $kode_pelanggan }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                                <input type="text"
                                    class="form-control @error('nama_pelanggan') border border-danger @enderror"
                                    id="nama_pelanggan" name="nama_pelanggan" placeholder="Contoh : John Doe"
                                    value="{{ old('nama_pelanggan') }}" />
                                @if ($errors->has('nama_pelanggan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_pelanggan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">Nomor Handphone</label>
                                <input type="text"
                                    class="form-control @error('no_hp') border border-danger @enderror" id="no_hp"
                                    name="no_hp" placeholder="Contoh : 081234567890" value="{{ old('no_hp') }}" />
                                @if ($errors->has('no_hp'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('no_hp') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date"
                                    class="form-control @error('tgl_lahir') border border-danger @enderror"
                                    id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}" />
                                @if ($errors->has('tgl_lahir'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_lahir') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
