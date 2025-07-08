<x-app-layout :title="'Tambah Pengeluaran'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('pengeluaran.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="no_pengeluaran" class="form-label">Nomor Pengeluaran</label>
                                <input type="text"
                                    class="form-control @error('no_pengeluaran') border border-danger @enderror"
                                    id="no_pengeluaran" name="no_pengeluaran" value="{{ $no_pengeluaran }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_pengeluaran" class="form-label">Tanggal Pengeluaran</label>
                                <input type="date"
                                    class="form-control @error('tgl_pengeluaran') border border-danger @enderror"
                                    id="tgl_pengeluaran" name="tgl_pengeluaran" value="{{ old('tgl_pengeluaran') }}" />
                                @if ($errors->has('tgl_pengeluaran'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_pengeluaran') }}
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="nominal" class="form-label">Nominal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text"
                                        class="form-control @error('nominal') border border-danger @enderror"
                                        id="nominal" name="nominal" placeholder="Contoh : 10.000"
                                        value="{{ old('nominal') }}" />
                                </div>
                                @if ($errors->has('nominal'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nominal') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="tipe_pengeluaran" class="form-label">Tipe Pengeluaran</label>
                                <select class="form-select @error('tipe_pengeluaran') border border-danger @enderror"
                                    id="tipe_pengeluaran" name="tipe_pengeluaran">
                                    <option value="" disabled selected> ==>> Pilih Pengeluaran <<== </option>
                                    <option value="Listrik">Listrik</option>
                                    <option value="Sewa">Sewa</option>
                                    <option value="Air">Air</option>
                                    <option value="Wifi">Wifi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                @if ($errors->has('tipe_pengeluaran'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tipe_pengeluaran') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nominalInput = document.getElementById('nominal');

            // Format on initial load if there's a value
            if (nominalInput.value) {
                nominalInput.value = formatNumber(nominalInput.value);
            }

            // Format as user types
            nominalInput.addEventListener('input', function(e) {
                // Store cursor position
                const cursorPos = this.selectionStart;
                const originalLength = this.value.length;

                // Remove non-numeric characters for processing
                let value = this.value.replace(/[^\d]/g, '');

                // Format the number
                if (value) {
                    this.value = formatNumber(value);
                }

                // Adjust cursor position based on change in length
                const newLength = this.value.length;
                const posDiff = newLength - originalLength;
                this.setSelectionRange(cursorPos + posDiff, cursorPos + posDiff);
            });

            // Handle form submission to remove formatting
            const form = nominalInput.closest('form');
            form.addEventListener('submit', function() {
                nominalInput.value = nominalInput.value.replace(/\./g, '');
            });

            // Function to format number with thousand separators
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        });
    </script>
</x-app-layout>
