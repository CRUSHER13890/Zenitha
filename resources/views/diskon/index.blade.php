<x-app-layout :title="'Diskon'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body table-responsive pt-0">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h4 class="my-auto header-title">Data {{ $title }}</h4>

                            <a href="{{ route('diskon.create') }}" class="btn btn-primary btn-icon-split btn-sm">
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
                        <!-- Akhir alert success -->

                        <!-- Alert error -->
                        @if ($message = Session::get('error'))
                            <div class="alert alert-warning">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <!-- Akhir alert error -->
                        <hr class="mt-0">
                        <table id="responsive-datatable"
                            class="table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Kode Diskon</th>
                                    <th>Nama Diskon</th>
                                    <th>Tipe</th>
                                    <th>Item</th>
                                    <th>Minimal Transaksi</th>
                                    <th>Persentase Diskon</th>
                                    <th>Maksimal Diskon</th>
                                    <th>Periode</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($diskon as $d)
                                    <tr>
                                        <td>{{ $d->kode_diskon }}</td>
                                        <td>{{ $d->nama_diskon }}</td>
                                        <td>
                                            @if($d->id_layanan)
                                                <span>Layanan</span>
                                            @else
                                                <span>Barang</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($d->id_layanan)
                                                {{ $d->layanan->nama_layanan ?? 'Tidak ada' }}
                                            @else
                                                {{ $d->barang->nama_barang ?? 'Tidak ada' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($d->min_transaksi)
                                                Rp. {{ number_format($d->min_transaksi, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $d->persentase_diskon }} %</td>
                                        <td>
                                            @if($d->max_diskon)
                                                Rp. {{ number_format($d->max_diskon, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($d->tanggal_mulai && $d->tanggal_selesai)
                                                {{ date('d/m/Y', strtotime($d->tanggal_mulai)) }} - 
                                                {{ date('d/m/Y', strtotime($d->tanggal_selesai)) }}
                                            @else
                                                Tidak ada periode
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('diskon.edit', $d->id) }}"
                                                class="btn btn-success btn-circle">
                                                Edit
                                            </a>

                                            <a onclick="deleteConfirm(this); return false;" href="#"
                                                data-id="{{ $d->id }}" data-kode="{{ $d->kode_diskon }}"
                                                class="btn btn-danger btn-circle">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Kode Diskon</th>
                                    <th>Nama Diskon</th>
                                    <th>Tipe</th>
                                    <th>Item</th>
                                    <th>Minimal Transaksi</th>
                                    <th>Persentase Diskon</th>
                                    <th>Maksimal Diskon</th>
                                    <th>Periode</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div> <!-- end row -->


    </div> <!-- container-fluid -->

    {{-- Confirmation Delete --}}

    <script>
        function deleteConfirm(e) {
            var tomboldelete = document.getElementById('btn-delete')
            id = e.getAttribute('data-id');
            kode = e.getAttribute('data-kode');

            var url3 = "{{ url('diskon/destroy/') }}";
            var url4 = url3.concat("/", id);
            tomboldelete.setAttribute("href", url4); //akan meload kontroller delete

            var pesan = "Data dengan Kode <b>"
            var pesan2 = " </b>akan dihapus"
            var res = kode;
            document.getElementById("xid").innerHTML = pesan.concat(res, pesan2);

            var myModal = new bootstrap.Modal(document.getElementById('deleteModal'), {
                keyboard: false
            });

            myModal.show();

        }
    </script>

    <!-- Logout Delete Confirmation-->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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