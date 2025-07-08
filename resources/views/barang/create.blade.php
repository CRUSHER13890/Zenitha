<x-app-layout :title="'Tambah Barang'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{ $title }}</h4>
                        <hr>
                        
                        <form method="POST" action="{{ route('barang.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="kode_barang" class="form-label">Kode Barang</label>
                                <input type="text"
                                    class="form-control @error('kode_barang') border border-danger @enderror"
                                    id="kode_barang" name="kode_barang" value="{{ $kode_barang }}" readonly />
                                @error('kode_barang')
                                    <div class="fw-light fs-6 mt-1 text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text"
                                    class="form-control @error('nama_barang') border border-danger @enderror"
                                    id="nama_barang" name="nama_barang" placeholder="Contoh : Body Lotion"
                                    value="{{ old('nama_barang') }}" />
                                @error('nama_barang')
                                    <div class="fw-light fs-6 mt-1 text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <img src="{{ asset('assets/images/barang/dummy-image.png') }}" alt="dummy-image.png"
                                    id="gambar_barang_preview" class="img-fluid" style="max-width: 289px" />
                            </div>
                            
                            <div class="mb-3">
                                <label for="gambar_barang" class="form-label">Gambar Barang</label>
                                <input type="file"
                                    class="form-control @error('gambar_barang') border border-danger @enderror"
                                    id="gambar_barang" name="gambar_barang" />
                                @error('gambar_barang')
                                    <div class="fw-light fs-6 mt-1 text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Vendor Section -->
                            <div class="mb-3">
                                <label class="form-label">Vendor & Harga</label>
                                <div id="vendor-container">
                                    <div class="vendor-item border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Vendor</label>
                                                <select class="form-select vendor-select" name="vendors[0][id]" required>
                                                    <option value="">Pilih Vendor</option>
                                                    @foreach ($vendor as $v)
                                                        <option value="{{ $v->id }}">{{ $v->nama_vendor }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Harga Satuan</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp.</span>
                                                    <input type="text" class="form-control harga-satuan" 
                                                           name="vendors[0][harga_satuan]" 
                                                           placeholder="10000" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Keuntungan (%)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control persentase-keuntungan" 
                                                           name="vendors[0][persentase_keuntungan]" 
                                                           placeholder="20" step="0.01" min="0" max="100" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Harga Jual</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp.</span>
                                                    <input type="text" class="form-control harga-jual" 
                                                           name="vendors[0][harga_jual]" 
                                                           placeholder="Otomatis" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-danger d-block remove-vendor" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-success btn-sm" id="add-vendor">
                                    <i class="fas fa-plus"></i> Tambah Vendor
                                </button>
                                
                                @error('vendors')
                                    <div class="fw-light fs-6 mt-1 text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="submit" class="btn btn-primary" value="Tambah" />
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let vendorIndex = 1;
            
            // Format number with thousand separators
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Parse formatted number back to integer
            function parseNumber(str) {
                return parseInt(str.replace(/\./g, '')) || 0;
            }

            // Get selected vendor IDs
            function getSelectedVendorIds() {
                const selectedIds = [];
                document.querySelectorAll('.vendor-select').forEach(select => {
                    if (select.value) {
                        selectedIds.push(select.value);
                    }
                });
                return selectedIds;
            }

            // Update vendor options for all selects
            function updateVendorOptions() {
                const selectedIds = getSelectedVendorIds();
                const allVendorSelects = document.querySelectorAll('.vendor-select');
                
                allVendorSelects.forEach(select => {
                    const currentValue = select.value;
                    const options = select.querySelectorAll('option');
                    
                    options.forEach(option => {
                        if (option.value === '') return; // Skip empty option
                        
                        if (selectedIds.includes(option.value) && option.value !== currentValue) {
                            option.style.display = 'none';
                            option.disabled = true;
                        } else {
                            option.style.display = 'block';
                            option.disabled = false;
                        }
                    });
                });
            }

            // Calculate harga jual
            function calculateHargaJual(vendorItem) {
                const hargaSatuan = vendorItem.querySelector('.harga-satuan');
                const persentase = vendorItem.querySelector('.persentase-keuntungan');
                const hargaJual = vendorItem.querySelector('.harga-jual');
                
                if (hargaSatuan.value && persentase.value) {
                    const harga = parseNumber(hargaSatuan.value);
                    const percent = parseFloat(persentase.value);
                    const result = harga + (harga * percent / 100);
                    hargaJual.value = formatNumber(Math.round(result));
                }
            }

            // Format harga satuan input
            function formatHargaSatuanInput(input) {
                input.addEventListener('input', function(e) {
                    const cursorPos = this.selectionStart;
                    const originalLength = this.value.length;

                    let value = this.value.replace(/[^\d]/g, '');

                    if (value) {
                        this.value = formatNumber(value);
                        const newLength = this.value.length;
                        const posDiff = newLength - originalLength;
                        this.setSelectionRange(cursorPos + posDiff, cursorPos + posDiff);
                    }

                    calculateHargaJual(this.closest('.vendor-item'));
                });
            }

            // Add vendor functionality
            document.getElementById('add-vendor').addEventListener('click', function() {
                const container = document.getElementById('vendor-container');
                const vendorItem = document.createElement('div');
                vendorItem.className = 'vendor-item border p-3 mb-3 rounded';
                vendorItem.innerHTML = `
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Vendor</label>
                            <select class="form-select vendor-select" name="vendors[${vendorIndex}][id]" required>
                                <option value="">Pilih Vendor</option>
                                @foreach ($vendor as $v)
                                    <option value="{{ $v->id }}">{{ $v->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control harga-satuan" 
                                       name="vendors[${vendorIndex}][harga_satuan]" 
                                       placeholder="10000" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Keuntungan (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control persentase-keuntungan" 
                                       name="vendors[${vendorIndex}][persentase_keuntungan]" 
                                       placeholder="20" step="0.01" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Harga Jual</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control harga-jual" 
                                       name="vendors[${vendorIndex}][harga_jual]" 
                                       placeholder="Otomatis" readonly>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger d-block remove-vendor">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                container.appendChild(vendorItem);
                vendorIndex++;
                
                // Add event listeners to new vendor item
                const hargaSatuanInput = vendorItem.querySelector('.harga-satuan');
                const persentaseInput = vendorItem.querySelector('.persentase-keuntungan');
                const removeBtn = vendorItem.querySelector('.remove-vendor');
                const vendorSelect = vendorItem.querySelector('.vendor-select');
                
                formatHargaSatuanInput(hargaSatuanInput);
                
                persentaseInput.addEventListener('input', function() {
                    calculateHargaJual(vendorItem);
                });
                
                // Add change event listener to vendor select
                vendorSelect.addEventListener('change', function() {
                    updateVendorOptions();
                });
                
                removeBtn.addEventListener('click', function() {
                    vendorItem.remove();
                    updateRemoveButtons();
                    updateVendorOptions(); // Update options when vendor is removed
                });
                
                updateRemoveButtons();
                updateVendorOptions(); // Update options for the new vendor select
            });

            // Update remove button visibility
            function updateRemoveButtons() {
                const vendorItems = document.querySelectorAll('.vendor-item');
                vendorItems.forEach(item => {
                    const removeBtn = item.querySelector('.remove-vendor');
                    if (vendorItems.length > 1) {
                        removeBtn.style.display = 'block';
                    } else {
                        removeBtn.style.display = 'none';
                    }
                });
            }

            // Initialize first vendor item
            const firstVendorItem = document.querySelector('.vendor-item');
            const firstHargaSatuan = firstVendorItem.querySelector('.harga-satuan');
            const firstPersentase = firstVendorItem.querySelector('.persentase-keuntungan');
            const firstVendorSelect = firstVendorItem.querySelector('.vendor-select');
            
            formatHargaSatuanInput(firstHargaSatuan);
            
            firstPersentase.addEventListener('input', function() {
                calculateHargaJual(firstVendorItem);
            });

            // Add change event listener to first vendor select
            firstVendorSelect.addEventListener('change', function() {
                updateVendorOptions();
            });

            // Remove formatting before form submission
            document.querySelector('form').addEventListener('submit', function() {
                document.querySelectorAll('.harga-satuan').forEach(input => {
                    input.value = parseNumber(input.value);
                });
                document.querySelectorAll('.harga-jual').forEach(input => {
                    input.value = parseNumber(input.value);
                });
            });

            // Image preview
            document.getElementById('gambar_barang').addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        document.getElementById('gambar_barang_preview').setAttribute('src', this.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>