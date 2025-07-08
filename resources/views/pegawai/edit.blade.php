<x-app-layout :title="'Edit Pegawai'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('pegawai.update', $pegawai->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="kode_pegawai" class="form-label">Kode Pegawai</label>
                                <input type="text"
                                    class="form-control @error('kode_pegawai') border border-danger @enderror"
                                    id="kode_pegawai" name="kode_pegawai" value="{{ $pegawai->kode_pegawai }}"
                                    readonly />
                            </div>
                            <div class="mb-3">
                                <label for="nama_pegawai" class="form-label">Nama Pegawai</label>
                                <input type="text"
                                    class="form-control @error('nama_pegawai') border border-danger @enderror"
                                    id="nama_pegawai" name="nama_pegawai" placeholder="Contoh : John Doe"
                                    value="{{ $pegawai->nama_pegawai }}" />
                                @if ($errors->has('nama_pegawai'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_pegawai') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">Nomor Handphone</label>
                                <input type="text"
                                    class="form-control @error('no_hp') border border-danger @enderror" id="no_hp"
                                    name="no_hp" placeholder="Contoh : 081234567890" value="{{ $pegawai->no_hp }}" />
                                @if ($errors->has('no_hp'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('no_hp') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin') border border-danger @enderror"
                                    id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="" disabled selected> ==>> Pilih Jenis Kelamin <<== </option>
                                    <option value="L" {{ $pegawai->jenis_kelamin == 'L' ? 'selected' : '' }}>
                                        Laki-Laki</option>
                                    <option value="P" {{ $pegawai->jenis_kelamin == 'P' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                @if ($errors->has('jenis_kelamin'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('jenis_kelamin') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date"
                                    class="form-control @error('tgl_lahir') border border-danger @enderror"
                                    id="tgl_lahir" name="tgl_lahir" value="{{ $pegawai->tgl_lahir }}" />
                                @if ($errors->has('tgl_lahir'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_lahir') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control @error('alamat') border border-danger @enderror" id="alamat" name="alamat"
                                    rows="5" placeholder="Contoh : Jl. Telekomunikasi No. 1" style="resize: none">{{ $pegawai->alamat }}</textarea>
                                @if ($errors->has('alamat'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('alamat') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Edit" />
                            <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
<!-- edit pegawai -->