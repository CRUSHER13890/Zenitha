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
                                        <div id="xpembelian"></div>
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
                                                            <th class="text-center">No Pembelian</th>
                                                            <th class="text-center">Tanggal</th>
                                                            <th class="text-center">Nama Vendor</th>
                                                            <th class=" text-center">Daftar Barang</th>
                                                            <th class="text-center">Jumlah</th>
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
            // Get current date
            const today = new Date();
            // Format to YYYY-MM
            const year = today.getFullYear();
            // Month is 0-indexed in JavaScript, so add 1 and pad with leading zero if needed
            const month = String(today.getMonth() + 1).padStart(2, '0');
            
            // Set the value of the period input
            document.getElementById('periode').value = `${year}-${month}`;
            
            // Trigger the process function to load data for current month
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
            var url = "{{ url('laporan/viewdatapembelian/') }}";
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
                        var awalanpembelian = "Laporan Pembelian ";
                        document.getElementById("xpembelian").innerHTML = tebal.concat(awalanpembelian,
                            akhirtebal);

                        //xperiode
                        var awalanperiode = "Periode ";
                        document.getElementById("xperiode").innerHTML = tebal.concat(awalanperiode,
                            periode_tampil, akhirtebal);

                        var nama = "ZENITHA BEAUTY CARE";
                        document.getElementById("xnama").innerHTML = tebal.concat(nama, akhirtebal);

                        // mengisi tabel
                        var total = 0;

                        $('tbody').html("");
                        $.each(response.pembelian, function(key, item) {
                            var no_pembelian = item.no_pembelian;
                            var tgl_pembelian = item.tgl_pembelian.substring(0, 10); //YYYY-MM-DD
                            $('tbody').append(
                                '<tr>\
                                                                                                                                                                <td class="text-center">' +
                                no_pembelian +
                                '</td>\
                                                                                                                                                                <td class="text-center">' +
                                tgl_pembelian +
                                '</td>\
                                                                                                                                                                <td>' +
                                item
                                .nama_vendor +
                                '</td>\
                                                                                                                                                                <td class="text-center">' +
                                item
                                .daftar_barang +
                                '</td>\
                                                                                                                                                                <td style="text-align:right;">Rp ' +
                                number_format(
                                    item
                                    .total) +
                                '</td>\
                                                                                                                                                                \</tr>'
                            );
                            total += item.total;
                        });
                        $('tbody').append(
                            '<tr>\
                                                                                                                                                                <th class="text-center" colspan=4>Total</th>\
                                                                                                                                                                <th style="text-align:right;">Rp ' +
                            number_format(total) +
                            '</th>\
                                                                                                                                                            \</tr>'
                        );
                    }
                }
            });
        }
    </script>
    <!-- Akhir Proses Jurnal -->
</x-app-layout>
