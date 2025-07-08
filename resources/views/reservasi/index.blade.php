<!-- reservasi.i -->
<x-app-layout :title="'Reservasi'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body table-responsive pt-0">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h4 class="my-auto header-title">Data {{ $title }}</h4>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('reservasi.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                    <span class="text">Tambah Data</span>
                                </a>
                            </div>
                        </div>

                        <div class="d-flex mb-3 align-items-center">
                            <!-- Alerts Section -->
                            <div class="flex-grow-1">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                                        {{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if ($message = Session::get('error'))
                                    <div class="alert alert-warning alert-dismissible fade show mb-0" role="alert">
                                        {{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if ($message = Session::get('approval_error'))
                                    <div class="alert alert-warning alert-dismissible fade show mb-0" role="alert">
                                        {{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                            </div>
                            <!-- End Alerts Section -->
                        </div>

                        <hr class="mt-0">
                        <!-- Date filter -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="input-group" style="max-width: 300px;">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <input type="date" id="filter_date" name="filter_date" class="form-control"
                                    value="{{ $filter_date }}" onchange="filterByDate()" placeholder="Filter Tanggal">
                            </div>
                        </div>

                        <!-- Table -->
                        <table id="responsive-datatable" class="table table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Nomor Reservasi</th>
                                    <th>Tanggal Reservasi</th>
                                    <th>Jam Reservasi</th>
                                    <th>Jam Selesai</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Nama Layanan</th>
                                    <th>Ruangan</th>
                                    <th>Harga Layanan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservasi as $d)
                                    <tr>
                                        <td>{{ $d->no_reservasi }}</td>
                                        <td>{{ date('d F Y', strtotime($d->tgl_reservasi)) }}</td>
                                        <td>{{ date('H:i', strtotime($d->waktu_mulai)) }}</td>
                                        <td>{{ $d->waktu_selesai ? date('H:i', strtotime($d->waktu_selesai)) : '-' }}
                                        </td>
                                        <td>{{ $d->nama_pelanggan }}</td>
                                        <td>{{ $d->nama_layanan }}</td>

                                        <td>{{ $d->ruangan ? $d->ruangan : '-' }}</td>
                                        <td>Rp. {{ number_format($d->harga_layanan, 0, ',', '.') }}</td>
                                        <td>{{ $d->status == 'pending' ? '-' : ucwords(str_replace('_', ' ', $d->status)) }}
                                        </td>
                                        <td>
                                            @if ($d->status == 'pending')
                                                <a href="{{ route('reservasi.approve', $d->id) }}"
                                                    class="btn btn-success btn-circle">
                                                    Disetujui
                                                </a>
                                                <a href="{{ route('reservasi.cancel', $d->id) }}"
                                                    class="btn btn-danger btn-circle">
                                                    Batal
                                                </a>
                                            @elseif ($d->status == 'Disetujui')
                                                <a href="{{ route('reservasi.cancel', $d->id) }}"
                                                    class="btn btn-danger btn-circle">
                                                    Batal
                                                </a>
                                            @endif
                                            @if (!in_array($d->status, ['pending', 'Selesai', 'Batal']))
                                                <a href="{{ route('reservasi.edit', $d->id) }}"
                                                    class="btn btn-success btn-circle">
                                                    Edit
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nomor Reservasi</th>
                                    <th>Tanggal Reservasi</th>
                                    <th>Jam Reservasi</th>
                                    <th>Jam Selesai</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Nama Layanan</th>
                                    <th>Ruangan</th>
                                    <th>Harga Layanan</th>
                                    <th>Status</th>
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
        function filterByDate() {
            const date = document.getElementById('filter_date').value;
            const url = new URL(window.location.href);

            if (date) {
                url.searchParams.set('filter_date', date);
            } else {
                url.searchParams.delete('filter_date');
            }

            // Simpan filter date di localStorage
            localStorage.setItem('reservasi_filter_date', date);

            // Redirect ke URL baru tanpa refresh halaman
            window.history.pushState({}, '', url);

            // Reload konten tabel dengan fetch
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Parse HTML response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Ambil konten tabel dari response
                    const newTable = doc.querySelector('#responsive-datatable');
                    const newAlert = doc.querySelector('.alert');

                    // Update tabel di halaman
                    if (newTable) {
                        document.querySelector('#responsive-datatable').innerHTML = newTable.innerHTML;
                    }

                    // Update alert jika ada
                    const alertContainer = document.querySelector('.card-body');
                    const existingAlert = alertContainer.querySelector('.alert');

                    if (newAlert) {
                        if (existingAlert) {
                            existingAlert.replaceWith(newAlert);
                        } else {
                            alertContainer.insertBefore(newAlert, alertContainer.firstChild);
                        }
                    } else if (existingAlert) {
                        existingAlert.remove();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Load filter dari localStorage saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const savedDate = localStorage.getItem('reservasi_filter_date');
            if (savedDate) {
                document.getElementById('filter_date').value = savedDate;
            }

            document.querySelectorAll('a[href*="reservasi/cancel"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const reservationId = this.getAttribute('href').split('/').pop();
                    const row = this.closest('tr');
                    const noReservasi = row.cells[0].textContent;
                    const namaPelanggan = row.cells[4].textContent;

                    // Set up the confirmation modal
                    document.getElementById("xid-cancel").innerHTML =
                        `Reservasi <b>${noReservasi}</b> atas nama <b>${namaPelanggan}</b> akan dibatalkan. Lanjutkan?`;

                    document.getElementById('btn-cancel-confirm').href = this.getAttribute('href');

                    // Show the modal
                    var cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
                    cancelModal.show();
                });
            });
        });


        function deleteConfirm(e) {
            var tomboldelete = document.getElementById('btn-delete')
            id = e.getAttribute('data-id');
            kode = e.getAttribute('data-kode');

            var url3 = "{{ url('reservasi/destroy/') }}";
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

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Konfirmasi Pembatalan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="xid-cancel"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                    <a id="btn-cancel-confirm" class="btn btn-danger" href="#">Ya, Batalkan</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
