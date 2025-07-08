<x-app-layout :title="'Edit Pembelian'">
    <div class="container-fluid">
<!-- editpembelian -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('pembelian.update', $pembelian->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="no_pembelian" class="form-label">Nomor Pembelian</label>
                                <input type="text"
                                    class="form-control @error('no_pembelian') border border-danger @enderror"
                                    id="no_pembelian" name="no_pembelian" value="{{ $pembelian->no_pembelian }}"
                                    readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_pembelian" class="form-label">Tanggal Pembelian</label>
                                <input type="date"
                                    class="form-control @error('tgl_pembelian') border border-danger @enderror"
                                    id="tgl_pembelian" name="tgl_pembelian" value="{{ $pembelian->tgl_pembelian }}" />
                                @if ($errors->has('tgl_pembelian'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_pembelian') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') border border-danger @enderror" id="keterangan" name="keterangan"
                                    rows="5" placeholder="Contoh : Pembelian Barang" style="resize: none">{{ $pembelian->keterangan }}</textarea>
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
                                    <option value="{{ $v->id }}"
                                        {{ $pembelian->id_vendor == $v->id ? 'selected' : '' }}>{{ $v->nama_vendor }}
                                    </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_vendor'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_vendor') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Edit" />
                            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>
