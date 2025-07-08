<!-- reservasi.e -->
<x-app-layout :title="'Edit Reservasi'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        @if ($errors->has('scheduling'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-alert-circle-outline me-1"></i>
                                {{ $errors->first('scheduling') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('reservasi.update', $reservasi->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="no_reservasi" class="form-label">Nomor Reservasi</label>
                                <input type="text"
                                    class="form-control @error('no_reservasi') border border-danger @enderror"
                                    id="no_reservasi" name="no_reservasi" value="{{ $reservasi->no_reservasi }}"
                                    readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_reservasi" class="form-label">Tanggal Reservasi</label>
                                <input type="date"
                                    class="form-control @error('tgl_reservasi') border border-danger @enderror"
                                    id="tgl_reservasi" name="tgl_reservasi"
                                    value="{{ old('tgl_reservasi', $reservasi->tgl_reservasi) }}" required
                                    onclick="this.showPicker()" />
                                @if ($errors->has('tgl_reservasi'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_reservasi') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="id_pelanggan" class="form-label">Pelanggan</label>
                                <select class="form-select @error('id_pelanggan') border border-danger @enderror"
                                    id="id_pelanggan" name="id_pelanggan" required>
                                    <option value="" disabled> ==>> Pilih Pelanggan <<== </option>
                                            @foreach ($pelanggan as $v)
                                    <option value="{{ $v->id }}"
                                        {{ old('id_pelanggan', $reservasi->id_pelanggan) == $v->id ? 'selected' : '' }}>
                                        {{ $v->kode_pelanggan }} - {{ $v->nama_pelanggan }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_pelanggan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_pelanggan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="id_layanan" class="form-label">Layanan</label>
                                <select class="form-select @error('id_layanan') border border-danger @enderror"
                                    id="id_layanan" name="id_layanan" required>
                                    <option value="" disabled> ==>> Pilih Layanan <<== </option>
                                            @foreach ($layanan as $v)
                                    <option value="{{ $v->id }}" data-harga="{{ $v->harga_layanan }}"
                                        {{ old('id_layanan', $reservasi->id_layanan) == $v->id ? 'selected' : '' }}>
                                        {{ $v->nama_layanan }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_layanan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('id_layanan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="harga_layanan" class="form-label">Harga Layanan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text"
                                        class="form-control @error('harga_layanan') border border-danger @enderror"
                                        id="harga_layanan" name="harga_layanan"
                                        value="{{ old('harga_layanan', number_format($layanan_selected->harga_layanan ?? 0, 0, ',', '.')) }}"
                                        required readonly />
                                </div>
                                @if ($errors->has('harga_layanan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('harga_layanan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="ruangan" class="form-label">Ruangan</label>
                                <select class="form-select @error('ruangan') border border-danger @enderror"
                                    id="ruangan" name="ruangan" required>
                                    <option value=""
                                        {{ old('ruangan', $reservasi->ruangan) == null ? 'selected' : '' }}> ==>> Pilih
                                        Ruangan <<== </option>
                                    <option value="Ruangan 1"
                                        {{ old('ruangan', $reservasi->ruangan) == 'Ruangan 1' ? 'selected' : '' }}>
                                        Ruangan 1</option>
                                    <option value="Ruangan 2"
                                        {{ old('ruangan', $reservasi->ruangan) == 'Ruangan 2' ? 'selected' : '' }}>
                                        Ruangan 2</option>
                                    <option value="Ruangan 3"
                                        {{ old('ruangan', $reservasi->ruangan) == 'Ruangan 3' ? 'selected' : '' }}>
                                        Ruangan 3</option>
                                </select>
                                @if ($errors->has('ruangan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('ruangan') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                                <input type="time"
                                    class="form-control @error('waktu_mulai') border border-danger @enderror"
                                    id="waktu_mulai" name="waktu_mulai"
                                    value="{{ old('waktu_mulai', $jadwal->waktu_mulai ?? ($reservasi->waktu_mulai ?? '')) }}"
                                    required onclick="this.showPicker()" />
                                @if ($errors->has('waktu_mulai'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('waktu_mulai') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                                <input type="time"
                                    class="form-control @error('waktu_selesai') border border-danger @enderror"
                                    id="waktu_selesai" name="waktu_selesai"
                                    value="{{ old('waktu_selesai', $jadwal->waktu_selesai ?? ($reservasi->waktu_selesai ?? '')) }}"
                                    required onclick="this.showPicker()" />
                                @if ($errors->has('waktu_selesai'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('waktu_selesai') }}
                                    </div>
                                @endif
                            </div>
                            <!-- Hidden status field with current value -->
                            <input type="hidden" name="status" value="{{ $reservasi->status }}" />

                            @if (!$reservasi->ruangan || !$reservasi->waktu_selesai)
                                <input type="hidden" name="status" value="Disetujui" />
                                <input type="submit" class="btn btn-primary" value="Jadwalkan" />
                            @else
                                <input type="hidden" name="status" value="{{ $reservasi->status }}" />
                                <input type="submit" class="btn btn-primary" value="Update" />
                            @endif
                            <a href="{{ route('reservasi.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectLayanan = document.getElementById('id_layanan');
            const inputHarga = document.getElementById('harga_layanan');

            // Update price when service is selected
            selectLayanan.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const harga = selected.getAttribute('data-harga');

                if (harga) {
                    // Format the price with thousand separators
                    inputHarga.value = new Intl.NumberFormat('id-ID').format(harga);
                } else {
                    inputHarga.value = '';
                }
            });

            // Trigger change event on page load to set initial price
            if (selectLayanan.selectedIndex > 0) {
                selectLayanan.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>
