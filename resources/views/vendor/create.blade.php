<x-app-layout :title="'Tambah Vendor'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('vendor.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="kode_vendor" class="form-label">Kode Vendor</label>
                                <input type="text"
                                    class="form-control @error('kode_vendor') border border-danger @enderror"
                                    id="kode_vendor" name="kode_vendor" value="{{ $kode_vendor }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="nama_vendor" class="form-label">Nama Vendor</label>
                                <input type="text"
                                    class="form-control @error('nama_vendor') border border-danger @enderror"
                                    id="nama_vendor" name="nama_vendor" placeholder="Contoh : PT Kimia Farma"
                                    value="{{ old('nama_vendor') }}" />
                                @if ($errors->has('nama_vendor'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_vendor') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="alamat_vendor" class="form-label">Alamat</label>
                                <textarea class="form-control @error('alamat_vendor') border border-danger @enderror" id="alamat_vendor"
                                    name="alamat_vendor" rows="5" placeholder="Contoh : Jl. Telekomunikasi No. 1" style="resize: none">{{ old('alamat_vendor') }}</textarea>
                                @if ($errors->has('alamat_vendor'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('alamat_vendor') }}
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
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('vendor.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
