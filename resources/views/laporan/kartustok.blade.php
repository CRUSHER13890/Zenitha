<x-app-layout :title="'Laporan Pembelian'">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="card-title fw-semibold mb-4">{{ $title }}</h5>
                        <div class="card">

                            <!-- Lokasi Jurnal Umum -->
                            <!-- Filter Periode Jurnal -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-sm-3">Pilih Periode</div>
                                            <div class="col-sm-9"><input type="month" class="form-control"
                                                    name="periode" id="periode" onchange="proses()"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Akhir Filter Periode Jurnal -->
                            <br>
                            <!-- Awal Tabel Jurnal -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                    </div>
                                    <div class="col-sm-12" align="center">
                                        <div id="xnama"></div>
                                    </div>
                                    <div class="col-sm-12" align="center">
                                        <div id="xkartustok"></div>
                                    </div>
                                    <div class="col-sm-12" align="center">
                                        <div id="xperiode"></div>
                                    </div>
                                    <br>
                                    <div class="responsive-table-plugin">
                                        <div class="table-rep-plugin">
                                            <div class="table-responsive" data-pattern="priority-columns">
                                                <table id="report" class="table table-bordered nowrap">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th class="text-center" style="vertical-align: middle"
                                                                rowspan="2">
                                                                Kode Barang
                                                            </th>
                                                            <th class="text-center" style="vertical-align: middle"
                                                                rowspan="2">Nama Barang</th>
                                                            <th class="text-center" colspan="4">Stok</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Awal</th>
                                                            <th class="text-center">Masuk</th>
                                                            <th class="text-center">Keluar</th>
                                                            <th class="text-center">Akhir</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Akhir Tabel Jurnal -->

                            <!-- Akhir Lokasi Jurnal Umum -->

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Proses Jurnal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const currentPeriod = `${year}-${month}`;
            
            document.getElementById('periode').value = currentPeriod;
            
            // Jalankan proses untuk menampilkan data bulan sekarang
            proses();
        });
        
        // fungsi number format
        function number_format(number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
            var n = !isFinite(+number) ? 0 : +number
            var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
            var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
            var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
            var s = ''

            var toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec)
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec)
            }

            // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || ''
                s[1] += new Array(prec - s[1].length + 1).join('0')
            }

            return s.join(dec)
        }

        // fungsi untuk merubah format YYYY-MM menjadi Bulan Tahun
        function rubah(periode) {
            // dapatkan tahun
            var tahun = periode.substring(0, 4);
            var bulan = periode.substring(5);
            switch (bulan) {
                case '01':
                    bln = "Januari";
                    break;
                case '02':
                    bln = "Februari";
                    break;
                case '03':
                    bln = "Maret";
                    break;
                case '04':
                    bln = "April";
                    break;
                case '05':
                    bln = "Mei";
                    break;
                case '06':
                    bln = "Juni";
                    break;
                case '07':
                    bln = "Juli";
                    break;
                case '08':
                    bln = "Agustus";
                    break;
                case '09':
                    bln = "September";
                    break;
                case '10':
                    bln = "Oktober";
                    break;
                case '11':
                    bln = "November";
                    break;
                case '12':
                    bln = "Desember";
                    break;
            }
            var hasil = bln.concat(" ", tahun)
            return hasil;
        }

        // fungsi untuk memproses perubahan nilai pada elemen input
        function proses() {

            // ambil nilai month dan year dari elemen input dalam format YYYY-MM
            var periode = document.getElementById("periode").value;
            var periode_tampil = rubah(periode);
            var url = "{{ url('laporan/viewdatakartustok/') }}";
            var url2 = url.concat("/", periode);
            // console.log(pilihan);
            $.ajax({
                type: "GET",
                url: url2,
                success: function(response) {
                    // console.log(response);
                    if (response.status == 404) {
                        // beri alert kalau gagal
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message,
                            icon: 'warning',
                            confirmButtonText: 'Ok',
                            background: '#2B333E',
                        });
                    } else {
                        // console.log(response);
                        // xperusahaan
                        var tebal = "<b>";
                        var akhirtebal = "</b>";

                        // xpembelian
                        var awalankartustok = "Kartu Stok";
                        document.getElementById("xkartustok").innerHTML = tebal.concat(awalankartustok,
                            akhirtebal);

                        //xperiode
                        var awalanperiode = "Periode ";
                        document.getElementById("xperiode").innerHTML = tebal.concat(awalanperiode,
                            periode_tampil, akhirtebal);

                        var nama = "ZENITHA BEAUTY CARE";
                        document.getElementById("xnama").innerHTML = tebal.concat(nama, akhirtebal);

                        $('tbody').html("");
                        $.each(response.kartustok, function(key, item) {
                            var akhir = item.kuantitas_persediaan - item.kuantitas_pengambilan;
                            $('tbody').append(
                                '<tr>\
                                        <td class="text-center">' +
                                item.kode_barang +
                                '</td>\
                                        <td class="text-center">' +
                                item.nama_barang +
                                '</td>\
                                        <td class="text-center">' +
                                item
                                .saldo_awal +
                                '</td>\
                                        <td class="text-center">' +
                                item
                                .kuantitas_persediaan +
                                '</td>\
                                        <td class="text-center">' +
                                item.kuantitas_pengambilan +
                                '</td>\
                                        <td class="text-center">' +
                                akhir +
                                '</td>\
                                        \</tr>'
                            );
                        });
                    }
                }
            });
        }
    </script>
    <!-- Akhir Proses Jurnal -->
</x-app-layout>
