<x-app-layout :title="'Buku Besar'">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="card-title fw-semibold mb-4">{{ $title }}</h5>
                        <div class="card">

                            <!-- Lokasi Buku Besar -->
                            <!-- Filter Periode Buku Besar -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-sm-3">Pilih Periode</div>
                                            <div class="col-sm-9"><input type="month" class="form-control"
                                                    name="periode" id="periode" onchange="proses()"></div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-3">Pilih Akun</div>
                                            <div class="col-sm-9">
                                                <select name="id_akun" id="id_akun" class="form-control"
                                                    onchange="proses()" required>
                                                    <option value="" disabled selected>- - - Pilih Akun - - -
                                                    </option>
                                                    @foreach ($akun as $ak)
                                                        <option value="{{ $ak->id }}-{{ $ak->nama_akun }}-{{ $ak->kode_akun }}">
                                                            {{ $ak->nama_akun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Akhir Filter Periode Buku Besar -->
                            <br>
                            <!-- Awal Tabel Buku Besar -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12" align="center">
                                            <div id="xnama"></div>
                                        </div>
                                        <div class="col-sm-12" align="center">
                                            <div id="xbukubesar"></div>
                                        </div>
                                        <div class="col-sm-12" align="center">
                                            <div id="xperiode"></div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="responsive-table-plugin">
                                        <div class="table-rep-plugin">
                                            <div class="table-responsive" data-pattern="priority-columns">
                                                <div class="row mb-3">
                                                    <div class="col-6 text-start">
                                                        <div id="namaAkun"></div>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <div id="noAkun"></div>
                                                    </div>
                                                </div>
                                                <table id="report" class="table table-bordered nowrap">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th class="text-center">Tanggal</th>
                                                            <th class="text-center">Keterangan</th>
                                                            <th class="text-center">Ref</th>
                                                            <th class="text-center">Debit</th>
                                                            <th class="text-center">Kredit</th>
                                                            <th class="text-center">Saldo Debit</th>
                                                            <th class="text-center">Saldo Kredit</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Akhir Tabel Buku Besar -->

                            <!-- Akhir Lokasi Buku Besar -->

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Proses Jurnal -->
    <script>
        // Set default value untuk input periode ke bulan sekarang
        document.addEventListener('DOMContentLoaded', function() {
            // Get current date
            const today = new Date();
            // Format to YYYY-MM
            const year = today.getFullYear();
            // Month is 0-indexed in JavaScript, so add 1 and pad with leading zero if needed
            const month = String(today.getMonth() + 1).padStart(2, '0');
            
            // Set the value of the period input
            document.getElementById('periode').value = `${year}-${month}`;
            
            // If an account is selected, process the data
            const accountSelect = document.getElementById('id_akun');
            if (accountSelect.value) {
                proses();
            }
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

        // fungsi untuk format tanggal dari YYYY-MM-DD menjadi DD-MMM
        function formatTanggal(tanggal) {
            // Format: YYYY-MM-DD to DD-MMM
            const date = new Date(tanggal);
            const day = date.getDate();
            const month = date.toLocaleString('default', { month: 'short' });
            return day + "-" + month;
        }

        // fungsi untuk memproses perubahan nilai pada elemen input
        function proses() {
            // ambil nilai month dan year dari elemen input dalam format YYYY-MM
            var periode = document.getElementById("periode").value;
            var akun = document.getElementById("id_akun").value; //format 111-Kas-101
            var parts = akun.split("-");
            var idakun = parts[0]; // ID akun
            var namaakun = parts[1]; // Nama akun
            var kodeakun = parts[2]; // Kode akun
            var periode_tampil = rubah(periode);
            var url = "{{ url('laporan/viewdatabukubesar/') }}";
            var url2 = url.concat("/", periode, "/", idakun);
            
            $.ajax({
                type: "GET",
                url: url2,
                success: function(response) {
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
                        // Set header information
                        var tebal = "<b>";
                        var akhirtebal = "</b>";

                        var nama = "Zenitha Beauty Care";
                        document.getElementById("xnama").innerHTML = tebal.concat(nama, akhirtebal);
                        
                        var awalanbukubesar = "Buku Besar";
                        document.getElementById("xbukubesar").innerHTML = tebal.concat(awalanbukubesar, akhirtebal);
                        
                        document.getElementById("xperiode").innerHTML = tebal.concat(periode_tampil, akhirtebal);
                        
                        // Set nama akun dan no akun
                        document.getElementById("namaAkun").innerHTML = tebal.concat("Nama Akun : ", namaakun, akhirtebal);
                        document.getElementById("noAkun").innerHTML = tebal.concat("No Akun : ", kodeakun, akhirtebal);

                        // mengisi tabel
                        $('tbody').html("");
                        
                        // untuk saldo awal - gunakan saldo awal dari COA
                        if (response.posisi == 'd') {
                            $('tbody').append(
                                '<tr>\
                                    <td>-</td>\
                                    <td><b>Saldo Awal</b></td>\
                                    <td>-</td>\
                                    <td style="text-align:right;">Rp ' + number_format(response.saldoawalcoa) + '</td>\
                                    <td>-</td>\
                                    <td style="text-align:right;">Rp ' + number_format(response.saldoawalcoa) + '</td>\
                                    <td style="text-align:right;">-</td>\
                                </tr>');
                            var saldo_debet = response.saldoawalcoa;
                            var saldo_kredit = 0;
                        } else {
                            $('tbody').append(
                                '<tr>\
                                    <td>-</td>\
                                    <td><b>Saldo Awal</b></td>\
                                    <td>-</td>\
                                    <td>-</td>\
                                    <td style="text-align:right;">Rp ' + number_format(response.saldoawalcoa) + '</td>\
                                    <td style="text-align:right;">-</td>\
                                    <td style="text-align:right;">Rp ' + number_format(response.saldoawalcoa) + '</td>\
                                </tr>');
                            var saldo_debet = 0;
                            var saldo_kredit = response.saldoawalcoa;
                        }

                        // untuk isi tabel - update logika perhitungan saldo
                        var d = 0;
                        var c = 0
                        $.each(response.bukubesar, function(index, item) {
                            var tgljurnal = formatTanggal(item.tgl_jurnal);
                            
                            // Gunakan nama_akun sebagai keterangan jika keterangan kosong atau undefined
                            var keterangan = item.keterangan || item.nama_akun || 'Transaksi';
                            
                            if ((response.posisi == 'd') && (item.posisi_dr_cr == 'd')) {
                                saldo_debet = saldo_debet + item.nominal;
                                d = d + item.nominal;
                                $('tbody').append(
                                    '<tr>\
                                        <td class="text-center">' + tgljurnal + '</td>\
                                        <td>' + keterangan + '</td>\
                                        <td class="text-center">' + item.no_jurnal + '</td>\
                                        <td style="text-align:right;">Rp ' + number_format(item.nominal) + '</td>\
                                        <td style="text-align:right;">-</td>\
                                        <td style="text-align:right;">Rp ' + number_format(saldo_debet) + '</td>\
                                        <td style="text-align:right;">-</td>\
                                    </tr>'
                                );
                            } else if ((response.posisi == 'd') && (item.posisi_dr_cr == 'c')) {
                                saldo_debet = saldo_debet - item.nominal;
                                c = c + item.nominal;
                                $('tbody').append(
                                    '<tr>\
                                        <td class="text-center">' + tgljurnal + '</td>\
                                        <td>' + keterangan + '</td>\
                                        <td class="text-center">' + item.no_jurnal + '</td>\
                                        <td style="text-align:right;">-</td>\
                                        <td style="text-align:right;">Rp ' + number_format(item.nominal) + '</td>\
                                        <td style="text-align:right;">Rp ' + number_format(saldo_debet) + '</td>\
                                        <td style="text-align:right;">-</td>\
                                    </tr>'
                                );
                            } else if ((response.posisi == 'c') && (item.posisi_dr_cr == 'd')) {
                                saldo_kredit = saldo_kredit - item.nominal;
                                d = d + item.nominal;
                                $('tbody').append(
                                    '<tr>\
                                        <td class="text-center">' + tgljurnal + '</td>\
                                        <td>' + keterangan + '</td>\
                                        <td class="text-center">' + item.no_jurnal + '</td>\
                                        <td style="text-align:right;">Rp ' + number_format(item.nominal) + '</td>\
                                        <td style="text-align:right;">-</td>\
                                        <td style="text-align:right;">-</td>\
                                        <td style="text-align:right;">Rp ' + number_format(saldo_kredit) + '</td>\
                                    </tr>'
                                );
                            } else if ((response.posisi == 'c') && (item.posisi_dr_cr == 'c')) {
                                saldo_kredit = saldo_kredit + item.nominal;
                                c = c + item.nominal;
                                $('tbody').append(
                                    '<tr>\
                                        <td class="text-center">' + tgljurnal + '</td>\
                                        <td>' + keterangan + '</td>\
                                        <td class="text-center">' + item.no_jurnal + '</td>\
                                        <td style="text-align:right;">-</td>\
                                        <td style="text-align:right;">Rp ' + number_format(item.nominal) + '</td>\
                                        <td style="text-align:right;">-</td>\
                                        <td style="text-align:right;">Rp ' + number_format(saldo_kredit) + '</td>\
                                    </tr>'
                                );
                            }
                        });

                        // footer saldo akhir
                        $('tbody').append(
                            '<tr>\
                                <td colspan="5" style="text-align: center;"><b>Saldo Akhir</b></td>\
                                <td style="text-align:right;">Rp ' + number_format(saldo_debet) + '</td>\
                                <td style="text-align:right;">Rp ' + number_format(saldo_kredit) + '</td>\
                            </tr>');
                    }
                }
            });
        }

        // Auto-load data when page loads if period and account are selected
        document.addEventListener('DOMContentLoaded', function() {
            // Set default period to current month
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            document.getElementById('periode').value = `${year}-${month}`;
            
            // If an account is selected, process the data
            const accountSelect = document.getElementById('id_akun');
            if (accountSelect.value) {
                proses();
            }
        });
    </script>
    <!-- Akhir Proses Jurnal -->
</x-app-layout>