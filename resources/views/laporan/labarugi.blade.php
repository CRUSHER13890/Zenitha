<x-app-layout :title="'Laba Rugi'">
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
                                    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="col-sm-3" style="padding-right: 10px;">Pilih Periode</div>
                                        <div class="col-sm-9" style="padding-left: 10px;">
                                            <input type="month" class="form-control" name="periode" id="periode" onchange="proses()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Akhir Filter Periode Jurnal -->
                            <br>
                            <!-- Awal Tabel Jurnal -->
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="row mb-1">
                                        <div class="col-sm-12" align="center">
                                            <div id="xnama"></div>
                                        </div>
                                        <div class="col-sm-12" align="center">
                                            <div id="xlabarugi"></div>
                                        </div>
                                        <div class="col-sm-12" align="center">
                                            <div id="xperiode"></div>
                                        </div>
                                    </div>
                                    <div class="responsive-table-plugin">
                                        <div class="table-rep-plugin">
                                            <div class="table-responsive" data-pattern="priority-columns">
                                                <table id="report" class="table table-borderless table-sm mb-0 mx-auto" style="max-width: 600px;">
                                                    <tbody style="line-height: 1.4">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Akhir Tabel Jurnal -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        #report.table-borderless tbody tr td {
            padding: 4px 8px; /* Added vertical padding for spacing */
        }
        
        #report.table-borderless tbody tr {
            margin: 0;
        }
        
        /* Add margin between rows */
        #report.table-borderless tbody tr {
            margin-bottom: 5px;
        }
        
        /* Style for left text (labels) */
        #report.table-borderless tbody tr td:first-child {
            text-align: left;
            padding-right: 20px;
            width: 50%;
        }
        
        /* Style for right text (values) */
        #report.table-borderless tbody tr td:last-child {
            text-align: right;
            padding-left: 20px;
            width: 50%;
        }
        
        /* For indented items */
        #report.table-borderless tbody tr td.indented {
            padding-left: 30px;
        }
        
        /* Add extra spacing after section headers */
        #report.table-borderless tbody tr:nth-of-type(1),
        #report.table-borderless tbody tr:nth-of-type(4),
        #report.table-borderless tbody tr:nth-of-type(11) {
            margin-top: 12px;
            margin-bottom: 8px;
        }
        
        /* Add space after category totals */
        #report.table-borderless tbody tr:nth-of-type(3),
        #report.table-borderless tbody tr:nth-of-type(10) {
            margin-bottom: 10px;
        }
    </style>




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
            var url = "{{ url('laporan/viewdatalabarugi/') }}";
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

                        //xlabarugi
                        var labarugi = "Laporan Laba Rugi";
                        document.getElementById("xlabarugi").innerHTML = tebal.concat(labarugi, akhirtebal);

                        //xperiode
                        var awalanperiode = "Periode ";
                        document.getElementById("xperiode").innerHTML = tebal.concat(awalanperiode,
                            periode_tampil, akhirtebal);

                        var nama = "ZENITHA BEAUTY CARE";
                        document.getElementById("xnama").innerHTML = tebal.concat(nama, akhirtebal);

                        // mengisi tabel
                        var total_pendapatan = 0;
                        var total_beban = 0;

                        $('tbody').html("");
                        $('tbody').append(
                            '<tr>\
                                                                                    <td><strong>Pendapatan</strong></td>\
                                                                                    <td></td>\
                                                                                </tr>'
                        );
                        $.each(response.pendapatan, function(key, item) {
                            $('tbody').append(
                                '<tr>\
                                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;' +
                                item.nama_akun +
                                '</td>\
                                                                                    <td align="right">Rp. ' +
                                number_format(item.nominal) +
                                '</td>\
                                                                                    </tr>'
                            );
                            total_pendapatan += item.nominal;
                        });
                        $('tbody').append(
                            '<tr>\
                                                                                    <td><strong>Total Pendapatan</strong></td>\
                                                                                    <td align="right"><strong>Rp. ' +
                            number_format(
                                total_pendapatan) + '</strong></td>\
                                                                                </tr>\
                                                                                <tr>\
                                                                                    <td><strong>Beban Operasional</strong></td>\
                                                                                    <td></td>\
                                                                                </tr>'
                        );
                        $.each(response.beban, function(key, item) {
                            $('tbody').append(
                                '<tr>\
                                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;' +
                                item.nama_akun +
                                '</td>\
                                                                                    <td align="right">Rp. ' +
                                number_format(item.nominal) +
                                '</td>\
                                                                                    </tr>'
                            );
                            total_beban += item.nominal;
                        });
                        var laba_rugi = total_pendapatan - total_beban;
                        var labaorrugi = laba_rugi >= 0 ? 'Laba' : 'Rugi';
                        $('tbody').append(
                            '<tr>\
                                                    <td><strong>Total Beban</strong></td>\
                                                    <td align="right"><strong>Rp. ' + number_format(total_beban) + '</strong></td>\
                                                </tr>\
                                                <tr>\
                                                    <td><strong>' + labaorrugi + '</strong></td>\
                                                    <td align="right"><strong>Rp. ' + number_format(Math.abs(laba_rugi)) + '</strong></td>\
                                                </tr>'
                        );
                    }
                }
            });
        }
    </script>
    <!-- Akhir Proses Jurnal -->
</x-app-layout>
