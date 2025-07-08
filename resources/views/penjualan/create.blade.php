<x-app-layout :title="'Tambah Penjualan'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('penjualan.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="no_penjualan" class="form-label">Nomor Penjualan</label>
                                <input type="text"
                                    class="form-control @error('no_penjualan') border border-danger @enderror"
                                    id="no_penjualan" name="no_penjualan" value="{{ $no_penjualan }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_penjualan" class="form-label">Tanggal Penjualan</label>
                                <input type="date"
                                    class="form-control @error('tgl_penjualan') border border-danger @enderror"
                                    id="tgl_penjualan" name="tgl_penjualan" value="{{ old('tgl_penjualan') }}" />
                                @if ($errors->has('tgl_penjualan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_penjualan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') border border-danger @enderror" id="keterangan" name="keterangan"
                                    rows="5" placeholder="Contoh : Penjualan Barang" style="resize: none">{{ old('keterangan') }}</textarea>
                                @if ($errors->has('keterangan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('keterangan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="id_pelanggan" class="form-label">Pelanggan</label>
                                <select class="form-select @error('id_pelanggan') border border-danger @enderror"
                                    id="id_pelanggan" name="id_pelanggan">
                                    <option value="" disabled selected> ==>> Pilih Pelanggan <<== </option>
                                            @foreach ($pelanggan as $v)
                                    <option value="{{ $v->id }}">{{ $v->nama_pelanggan }}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_pelanggan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_pelanggan') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
