<x-app-layout :title="'Tambah Jadwal'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('jadwal.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="kode_jadwal" class="form-label">Kode Jadwal</label>
                                <input type="text"
                                    class="form-control @error('kode_jadwal') border border-danger @enderror"
                                    id="kode_jadwal" name="kode_jadwal" value="{{ $kode_jadwal }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="waktu_mulai" class="form-label">Waktu</label>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <input type="time"
                                            class="form-control @error('waktu_mulai') border border-danger @enderror"
                                            id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai') }}" />
                                        @if ($errors->has('waktu_mulai'))
                                            <div class="fw-light fs-6 mt-1 text-danger">
                                                {{ $errors->first('waktu_mulai') }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="col-sm-1 text-center my-auto">-</span>
                                    <div class="col-sm-2">
                                        <input type="time"
                                            class="form-control @error('waktu_selesai') border border-danger @enderror"
                                            id="waktu_selesai" name="waktu_selesai"
                                            value="{{ old('waktu_selesai') }}" />
                                        @if ($errors->has('waktu_selesai'))
                                            <div class="fw-light fs-6 mt-1 text-danger">
                                                {{ $errors->first('waktu_selesai') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="ruangan" class="form-label">Ruangan</label>
                                <input type="text"
                                    class="form-control @error('ruangan') border border-danger @enderror" id="ruangan"
                                    name="ruangan" placeholder="Contoh : Ruang 1" value="{{ old('ruangan') }}" />
                                @if ($errors->has('ruangan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('ruangan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="id_layanan" class="form-label">Layanan</label>
                                <select class="form-select @error('id_layanan') border border-danger @enderror"
                                    id="id_layanan" name="id_layanan">
                                    <option value="" disabled selected> ==>> Pilih Layanan <<== </option>
                                            @foreach ($layanan as $v)
                                    <option value="{{ $v->id }}">{{ $v->nama_layanan }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_layanan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_layanan') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
