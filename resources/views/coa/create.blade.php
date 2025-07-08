<x-app-layout :title="'Tambah Chart of Account'">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>

                        <hr>
                        <form method="POST" action="{{ route('coa.store') }}" onsubmit="return confirmTambahCoa();">
                            @csrf
                            <div class="mb-3">
                                <label for="kode_akun" class="form-label">Kode Akun</label>
                                <input type="text"
                                    class="form-control @error('kode_akun') border border-danger @enderror"
                                    id="kode_akun" name="kode_akun" placeholder="Contoh : 111"
                                    value="{{ old('kode_akun') }}" />
                                @if ($errors->has('kode_akun'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('kode_akun') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="header_akun" class="form-label">Header Akun</label>
                                <input type="text"
                                    class="form-control @error('header_akun') border border-danger @enderror"
                                    id="header_akun" name="header_akun" placeholder="Contoh : 1"
                                    value="{{ old('header_akun') }}" />
                                @if ($errors->has('header_akun'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('header_akun') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="nama_akun" class="form-label">Nama Akun</label>
                                <input type="text"
                                    class="form-control @error('nama_akun') border border-danger @enderror"
                                    id="nama_akun" name="nama_akun" placeholder="Contoh : Kas"
                                    value="{{ old('nama_akun') }}" />
                                @if ($errors->has('nama_akun'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('nama_akun') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="saldo_awal" class="form-label">Saldo Awal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text"
                                        class="form-control @error('saldo_awal') border border-danger @enderror"
                                        id="saldo_awal" name="saldo_awal" placeholder="Contoh : 1.000.000"
                                        value="{{ old('saldo_awal') }}" />
                                </div>
                                @if ($errors->has('saldo_awal'))
                                    <div class="fw-light fs-6 mt-1 text-danger">
                                        {{ $errors->first('saldo_awal') }}
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary" onclick="showCoaWarning()">Tambah</button>
                            <a href="{{ route('coa.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div> <!-- container -->
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const saldoAwalInput = document.getElementById('saldo_awal');

        // Format on initial load if there's a value
        if (saldoAwalInput.value) {
            saldoAwalInput.value = formatNumber(saldoAwalInput.value);
        }

        // Format as user types
        saldoAwalInput.addEventListener('input', function(e) {
            // Store cursor position
            const cursorPos = this.selectionStart;
            const originalLength = this.value.length;

            // Remove non-numeric characters for processing
            let value = this.value.replace(/[^\d]/g, '');

            // Format the number
            if (value) {
                this.value = formatNumber(value);
            } else {
                this.value = '';
            }

            // Adjust cursor position based on change in length
            const newLength = this.value.length;
            const posDiff = newLength - originalLength;
            this.setSelectionRange(cursorPos + posDiff, cursorPos + posDiff);
        });

        // Function to format number with thousand separators
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });

    function showCoaWarning() {
        Swal.fire({
            title: 'Peringatan!',
            text: 'Setelah Chart of Account ditambahkan, data TIDAK DAPAT dihapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, tambahkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove formatting before submitting
                const saldoAwalInput = document.getElementById('saldo_awal');
                if (saldoAwalInput.value) {
                    saldoAwalInput.value = saldoAwalInput.value.replace(/\./g, '');
                }
                document.querySelector('form').submit();
            }
        });
    }
</script>