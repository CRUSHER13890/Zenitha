<x-app-layout :title="'Tambah Pembelian'">
    <div class="container-fluid">
<!-- createpembelian -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>

                        <!-- Alert warning -->
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Perhatian!</strong> Hati-hati data tidak bisa di edit setelah disimpan. Pastikan
                            data yang dimasukkan sudah benar.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <form method="POST" action="{{ route('pembelian.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="no_pembelian" class="form-label">Nomor Pembelian</label>
                                <input type="text"
                                    class="form-control @error('no_pembelian') border border-danger @enderror"
                                    id="no_pembelian" name="no_pembelian" value="{{ $no_pembelian }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_pembelian" class="form-label">Tanggal Pembelian</label>
                                <input type="date"
                                    class="form-control @error('tgl_pembelian') border border-danger @enderror"
                                    id="tgl_pembelian" name="tgl_pembelian" value="{{ old('tgl_pembelian') }}" />
                                @if ($errors->has('tgl_pembelian'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_pembelian') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') border border-danger @enderror" id="keterangan" name="keterangan"
                                    rows="5" placeholder="Contoh : Pembelian Barang" style="resize: none">{{ old('keterangan') }}</textarea>
                                @if ($errors->has('keterangan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('keterangan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="id_vendor" class="form-label">Vendor</label>
                                <select class="form-select @error('id_vendor') border border-danger @enderror"
                                    id="id_vendor" name="id_vendor">
                                    <option value="" disabled selected> ==>> Pilih Vendor <<== </option>
                                            @foreach ($vendor as $v)
                                    <option value="{{ $v->id }}">{{ $v->nama_vendor }}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_vendor'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_vendor') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
