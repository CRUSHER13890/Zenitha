<x-app-layout :title="'Edit Modal'">
    <div class="container-fluid">
<!-- editmodal -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('modal.update', $modal->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="kode_modal" class="form-label">Nomor Modal</label>
                                <input type="text"
                                    class="form-control @error('kode_modal') border border-danger @enderror"
                                    id="kode_modal" name="kode_modal" value="{{ $modal->kode_modal }}"
                                    readonly />
                            </div>
                            <div class="mb-3">
                                <label for="tgl_modal" class="form-label">Tanggal Modal</label>
                                <input type="date"
                                    class="form-control @error('tgl_modal') border border-danger @enderror"
                                    id="tgl_modal" name="tgl_modal"
                                    value="{{ $modal->tgl_modal }}" />
                                @if ($errors->has('tgl_modal'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('tgl_modal') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <input type="text"
                                    class="form-control @error('keterangan') border border-danger @enderror"
                                    id="keterangan" name="keterangan" value="{{ $modal->keterangan }}" />
                                @if ($errors->has('keterangan'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('keterangan') }}
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
                                        value="{{ $modal->nominal }}" />
                                </div>
                                @if ($errors->has('nominal'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nominal') }}
                                    </div>
                                @endif
                            </div>
                            <input type="submit" class="btn btn-primary" value="Edit" />
                            <a href="{{ route('modal.index') }}" class="btn btn-secondary">Cancel</a>
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
