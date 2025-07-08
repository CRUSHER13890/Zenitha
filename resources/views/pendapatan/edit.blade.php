<x-app-layout :title="'Edit Pendapatan Jasa'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('pendapatan.update', $pendapatan->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="no_pendapatan" class="form-label">Nomor Pendapatan</label>
                                <input type="text"
                                    class="form-control @error('no_pendapatan') border border-danger @enderror"
                                    id="no_pendapatan" name="no_pendapatan" value="{{ $pendapatan->no_pendapatan }}"
                                    readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_pendapatan" class="form-label">Tanggal Pendapatan</label>
                                <input type="date"
                                    class="form-control @error('tgl_pendapatan') border border-danger @enderror"
                                    id="tgl_pendapatan" name="tgl_pendapatan"
                                    value="{{ $pendapatan->tgl_pendapatan }}" />
                                @if ($errors->has('tgl_pendapatan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_pendapatan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="id_pelanggan" class="form-label">Pelanggan</label>
                                <select class="form-select @error('id_pelanggan') border border-danger @enderror"
                                    id="id_pelanggan" name="id_pelanggan">
                                    <option value="" disabled selected> ==>> Pilih Pelanggan <<== </option>
                                            @foreach ($pelanggan as $v)
                                    <option value="{{ $v->id }}"
                                        {{ $pendapatan->id_pelanggan == $v->id ? 'selected' : '' }}>
                                        {{ $v->nama_pelanggan }}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_pelanggan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_pelanggan') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Edit" />
                            <a href="{{ route('pendapatan.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
