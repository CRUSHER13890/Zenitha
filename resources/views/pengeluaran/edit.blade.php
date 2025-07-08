<x-app-layout :title="'Edit Pengeluaran'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('pengeluaran.update', $pengeluaran->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="no_pengeluaran" class="form-label">Nomor Pengeluaran</label>
                                <input type="text"
                                    class="form-control @error('no_pengeluaran') border border-danger @enderror"
                                    id="no_pengeluaran" name="no_pengeluaran" value="{{ $pengeluaran->no_pengeluaran }}"
                                    readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_pengeluaran" class="form-label">Tanggal Pengeluaran</label>
                                <input type="date"
                                    class="form-control @error('tgl_pengeluaran') border border-danger @enderror"
                                    id="tgl_pengeluaran" name="tgl_pengeluaran"
                                    value="{{ $pengeluaran->tgl_pengeluaran }}" />
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
                                        value="{{ $pengeluaran->nominal }}" />
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
                                    <option value="Listrik"
                                        {{ $pengeluaran->tipe_pengeluaran == 'Listrik' ? 'selected' : '' }}>Listrik
                                    </option>
                                    <option value="Sewa"
                                        {{ $pengeluaran->tipe_pengeluaran == 'Sewa' ? 'selected' : '' }}>Sewa</option>
                                    <option value="Air"
                                        {{ $pengeluaran->tipe_pengeluaran == 'Air' ? 'selected' : '' }}>Air</option>
                                    <option value="Wifi"
                                        {{ $pengeluaran->tipe_pengeluaran == 'Wifi' ? 'selected' : '' }}>Wifi</option>
                                    <option value="Lainnya"
                                        {{ $pengeluaran->tipe_pengeluaran == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                    </option>
                                </select>
                                @if ($errors->has('tipe_pengeluaran'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tipe_pengeluaran') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Edit" />
                            <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const nominalInput = document.getElementById('nominal');

                // Format on initial load
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
    @endpush
</x-app-layout>
