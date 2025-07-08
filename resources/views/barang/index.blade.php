<x-app-layout :title="'Barang'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body table-responsive pt-0">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h4 class="my-auto header-title">Data {{ $title }}</h4>
                            <a href="{{ route('barang.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                                <span class="icon text-white-50">
                                    <i class="fas fa-plus"></i>
                                </span>
                                <span class="text">Tambah Data</span>
                            </a>
                        </div>
                        
                        <!-- Alert success -->
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        <!-- Alert error -->
                        @if ($message = Session::get('error'))
                            <div class="alert alert-warning">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        
                        <hr class="mt-0">
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Gambar</th>
                                    <th>Vendor & Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $b)
                                    <tr>
                                        <td>{{ $b->kode_barang }}</td>
                                        <td>{{ $b->nama_barang }}</td>
                                        <td>
                                            <img src="{{ asset('assets/images/barang/' . $b->gambar_barang) }}" 
                                                 alt="{{ $b->nama_barang }}" 
                                                 class="img-fluid" 
                                                 style="max-width: 50px; max-height: 50px;">
                                        </td>
                                        <td>
                                            @if($b->barangVendors->count() > 0)
                                                @foreach($b->barangVendors as $bv)
                                                    <div class="mb-2 p-2 border rounded">
                                                        <strong>{{ $bv->vendor->nama_vendor }}</strong><br>
                                                        <small>
                                                            Harga Satuan: Rp. {{ number_format($bv->harga_satuan, 0, ',', '.') }}<br>
                                                            Harga Jual: Rp. {{ number_format($bv->harga_jual, 0, ',', '.') }}<br>
                                                            Keuntungan: {{ $bv->persentase_keuntungan }}%
                                                        </small>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Belum ada vendor</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-success btn-sm mb-1">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a onclick="deleteConfirm(this); return false;" 
                                               href="#" 
                                               data-id="{{ $b->id }}" 
                                               data-kode="{{ $b->kode_barang }}"
                                               class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Delete --}}
    <script>
        function deleteConfirm(e) {
            var tomboldelete = document.getElementById('btn-delete')
            id = e.getAttribute('data-id');
            kode = e.getAttribute('data-kode');

            var url3 = "{{ url('barang/destroy/') }}";
            var url4 = url3.concat("/", id);
            tomboldelete.setAttribute("href", url4);

            var pesan = "Data dengan Kode <b>"
            var pesan2 = " </b>akan dihapus beserta semua data vendor terkait"
            var res = kode;
            document.getElementById("xid").innerHTML = pesan.concat(res, pesan2);

            var myModal = new bootstrap.Modal(document.getElementById('deleteModal'), {
                keyboard: false
            });

            myModal.show();
        }
    </script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apakah anda yakin?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="xid"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <a id="btn-delete" class="btn btn-danger" href="#">Hapus</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>