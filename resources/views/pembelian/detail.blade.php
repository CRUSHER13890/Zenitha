<x-app-layout :title="'Detail Pembelian'">
    <div class="container-fluid">
        <!-- Alert success -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <!-- Akhir alert success -->

        <!-- Alert success -->
        @if ($message = Session::get('error'))
            <div class="alert alert-warning">
                <p>{{ $message }}</p>
            </div>
        @endif
        <!-- Akhir alert success -->
        <div class="row">
            <!-- DataTales Example -->
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <h1 class="h3 mb-2 text-gray-800 text-primary">Data {{ $title }}</h1>
                        </div>
                        <div class="float-end">
                            <a class="btn btn-primary waves-effect waves-light" href="{{ route('pembelian.index') }}">
                                Kembali
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="sub-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="float-start mt-3">
                                    <p class="m-t-10"><strong>Nomor Pembelian : </strong> {{ $pembelian->no_pembelian }}
                                    </p>
                                    <p class="m-t-10"><strong>Tanggal Pembelian : </strong>
                                        {{ date('d F Y', strtotime($pembelian->tgl_pembelian)) }}
                                    </p>
                                </div>
                                <div class="float-end mt-3">
                                    <p class="m-t-10"><strong>Keterangan : </strong> {{ $pembelian->keterangan }} </p>
                                    <p class="m-t-10"><strong>Nama Vendor : </strong> {{ $pembelian->nama_vendor }} </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('pembelian.storedetail') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_pembelian_header" id="id_pembelian_header"
                            value="{{ $pembelian->id }}">
                        <input type="hidden" name="no_pembelian" id="no_pembelian"
                            value="{{ $pembelian->no_pembelian }}">
                        <div class="mb-3">
                            <label for="id_barang" class="form-label">Barang</label>
                            <select class="form-select @error('id_barang') border border-danger @enderror"
                                id="id_barang" name="id_barang" required>
                                <option value="" disabled selected> ==>> Pilih Barang <<== </option>
                                        @foreach ($barang as $item)
                                <option value="{{ $item->id }}" data-harga="{{ $item->harga_satuan }}"
                                    data-harga-formatted="{{ $item->harga_formatted }}">
                                    {{ $item->nama_barang }} (Rp {{ $item->harga_formatted }})
                                </option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_barang'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('id_barang') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="kuantitas" class="form-label">Kuantitas</label>
                            <input type="text" inputmode="numeric"
                                class="form-control @error('kuantitas') border border-danger @enderror" id="kuantitas"
                                placeholder="Contoh : 10 pcs"
                                value="{{ old('kuantitas') ? old('kuantitas') . ' pcs' : '' }}" />
                            <!-- Hidden field for raw kuantitas value -->
                            <input type="hidden" id="kuantitas-raw" name="kuantitas" value="{{ old('kuantitas') }}">
                            @if ($errors->has('kuantitas'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('kuantitas') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="harga_satuan" class="form-label">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" inputmode="numeric"
                                    class="form-control @error('harga_satuan') border border-danger @enderror"
                                    id="harga_satuan" placeholder="Contoh : 10.000"
                                    value="{{ old('harga_satuan') ? number_format(old('harga_satuan'), 0, ',', '.') : '' }}"
                                    readonly />
                                <!-- Hidden field for raw harga_satuan value -->
                                <input type="hidden" id="harga-satuan-raw" name="harga_satuan"
                                    value="{{ old('harga_satuan') }}">
                            </div>
                            @if ($errors->has('harga_satuan'))
                                <div class="fw-light fs-6 mt-1 text-danger">
                                    {{ $errors->first('harga_satuan') }}
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="subtotal" class="form-label">Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="subtotal" readonly />
                            </div>
                        </div>
                        <!-- untuk tombol simpan -->
                        <input class="btn btn-primary waves-effect waves-light" type="submit" value="Simpan">

                        <!-- untuk tombol batal simpan -->
                        <a class="btn btn-secondary waves-effect" href="{{ url('/pembelian') }}"
                            role="button">Batal</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered dt-responsive nowrap" id="responsive-datatable">
                        <thead>
                            <tr align="center">
                                <th>Nama Barang</th>
                                <th>Kuantitas</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($pembelian_detail as $p)
                                <tr>
                                    <td>{{ $p->nama_barang }}</td>
                                    <td align="center">{{ number_format($p->kuantitas, 0, ',', '.') }} pcs</td>
                                    <td align="right">Rp.
                                        {{ number_format($p->harga_vendor ?? $p->harga_satuan, 0, ',', '.') }}</td>
                                    <td align="right">Rp.
                                        {{ number_format(($p->harga_vendor ?? $p->harga_satuan) * $p->kuantitas, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <a onclick="deleteConfirm(this); return false;" href="#"
                                            data-id="{{ $p->id }}"
                                            data-barang="{{ $p->nama_barang }} - {{ $p->kuantitas }} - Rp. {{ number_format($p->harga_vendor ?? $p->harga_satuan, 0, ',', '.') }}"
                                            class="btn btn-danger btn-circle">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                                @php
                                    $total += ($p->harga_vendor ?? $p->harga_satuan) * $p->kuantitas;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">Total Pembelian</td>
                                <td align="right">Rp. {{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Delete --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format number with ID locale
            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

            // Parse formatted number back to raw number
            function parseFormattedNumber(formattedString) {
                if (!formattedString) return 0;
                return parseInt(formattedString.replace(/\D/g, ''));
            }

            // Update harga satuan when barang is selected
            function updateHargaSatuan(barangId) {
                const selectedOption = document.querySelector(`#id_barang option[value="${barangId}"]`);

                if (selectedOption) {
                    const hargaSatuan = selectedOption.getAttribute('data-harga');
                    const hargaFormatted = selectedOption.getAttribute('data-harga-formatted');

                    document.getElementById('harga_satuan').value = hargaFormatted;
                    document.getElementById('harga-satuan-raw').value = hargaSatuan;

                    calculateSubtotal();
                }
            }

            // Calculate subtotal
            function calculateSubtotal() {
                const kuantitas = parseInt(document.getElementById('kuantitas-raw').value) || 0;
                const hargaSatuan = parseInt(document.getElementById('harga-satuan-raw').value) || 0;
                const subtotal = kuantitas * hargaSatuan;

                document.getElementById('subtotal').value = formatNumber(subtotal);
            }

            // Quantity input formatting
            const kuantitasInput = document.getElementById('kuantitas');

            kuantitasInput.addEventListener('input', function(e) {
                // Get current cursor position
                const cursorPos = this.selectionStart;
                const oldValue = this.value;

                // Extract numeric part
                let rawValue = parseFormattedNumber(oldValue);

                // Handle backspace
                if (e.inputType === 'deleteContentBackward' &&
                    cursorPos <= oldValue.length - 4 &&
                    cursorPos > 0) {
                    const numStr = rawValue.toString();
                    rawValue = numStr.length > 1 ? parseInt(numStr.slice(0, -1)) : 0;
                }

                // Format with thousand separator and add "pcs"
                const newValue = rawValue > 0 ? formatNumber(rawValue) + ' pcs' : 'pcs';
                this.value = newValue;
                document.getElementById('kuantitas-raw').value = rawValue;

                // Restore cursor position
                const newCursorPos = Math.min(cursorPos, newValue.length - 4);
                this.setSelectionRange(newCursorPos, newCursorPos);

                calculateSubtotal();
            });

            // Prevent cursor from going into "pcs"
            kuantitasInput.addEventListener('keydown', function(e) {
                const cursorPos = this.selectionStart;
                const value = this.value;

                if (cursorPos > value.length - 4 &&
                    (e.key === 'ArrowRight' || e.key === 'Delete')) {
                    e.preventDefault();
                }

                if (cursorPos === value.length - 4 && e.key === 'Delete') {
                    e.preventDefault();
                }

                if (cursorPos === value.length && e.key === 'Backspace') {
                    this.setSelectionRange(value.length - 4, value.length - 4);
                    e.preventDefault();
                }
            });

            // Initialize quantity field
            if (!kuantitasInput.value) {
                kuantitasInput.value = 'pcs';
            }

            // Barang selection change event
            document.getElementById('id_barang').addEventListener('change', function() {
                if (this.value) {
                    updateHargaSatuan(this.value);
                }
            });

            // Initialize delete confirmation modal
            window.deleteConfirm = function(e) {
                const tomboldelete = document.getElementById('btn-delete');
                const id = e.getAttribute('data-id');
                const barang = e.getAttribute('data-barang');

                const url = "{{ url('pembelian/destroydetail/') }}/" + id;
                tomboldelete.setAttribute("href", url);

                document.getElementById("xid").innerHTML =
                    `Data Barang <b>${barang}</b> akan dihapus`;

                new bootstrap.Modal(document.getElementById('deleteModal'), {
                    keyboard: false
                }).show();
            };
        });
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
