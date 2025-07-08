<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;

class LaporanController extends Controller
{
    public function jurnalumum()
    {
        return view('laporan.jurnalumum', ['title' => 'Jurnal Umum']);
    }

    public function viewdatajurnalumum($periode)
    {
        $jurnal = Jurnal::viewjurnalumum($periode);
        if ($jurnal) {
            return response()->json([
                'status' => 200,
                'jurnal' => $jurnal,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function bukubesar()
    {
        $akun = Jurnal::viewakunbukubesar();
        return view(
            'laporan.bukubesar',
            [
                'akun' => $akun,
                'title' => 'Buku Besar',
            ]
        );
    }

    public function viewdatabukubesar($periode, $akun)
    {
        $saldoawal = Jurnal::viewsaldobukubesar($periode, $akun);
        $saldoawalcoa = Jurnal::viewsaldoawalcoa($akun); // Tambahkan ini
        $posisi = Jurnal::viewposisisaldonormalakun($akun);

        $bukubesar = Jurnal::viewdatabukubesar($periode, $akun);
        if (count($bukubesar) > 0 || $saldoawalcoa > 0) { // Update kondisi
            return response()->json([
                'status' => 200,
                'bukubesar' => $bukubesar,
                'saldoawal' => $saldoawal,
                'saldoawalcoa' => $saldoawalcoa, // Tambahkan ini
                'posisi' => $posisi,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function pembelian()
    {
        return view('laporan.pembelian', ['title' => 'Laporan Pembelian']);
    }

    public function viewdatapembelian($periode)
    {
        $pembelian = Jurnal::viewpembelian($periode);
        if (count($pembelian) > 0) {
            return response()->json([
                'status' => 200,
                'pembelian' => $pembelian,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function penjualan()
    {
        return view('laporan.penjualan', ['title' => 'Laporan Penjualan']);
    }

    public function viewdatapenjualan($periode)
    {
        $penjualan = Jurnal::viewpenjualan($periode);
        if (count($penjualan) > 0) {
            return response()->json([
                'status' => 200,
                'penjualan' => $penjualan,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function labarugi()
    {
        return view('laporan.labarugi', ['title' => 'Laporan Laba Rugi']);
    }

    public function viewdatalabarugi($periode)
    {
        $pendapatan_raw = Jurnal::viewlabarugi($periode, 4);
        $beban_raw = Jurnal::viewlabarugi($periode, 5);

        // Hitung saldo akhir dengan memasukkan saldo awal
        $pendapatan = Jurnal::calculateSaldoAkhirLabaRugi($pendapatan_raw, 4);
        $beban = Jurnal::calculateSaldoAkhirLabaRugi($beban_raw, 5);

        $total_pendapatan = 0;
        $total_diskon_pendapatan = 0;
        $total_diskon_penjualan = 0;

        // Process pendapatan and separate diskon types
        $pendapatan_processed = [];
        foreach ($pendapatan as $p) {
            $akun_name = strtolower($p->nama_akun);

            // Handle different types of discounts
            if (
                $akun_name === 'diskon pendapatan' ||
                strpos($akun_name, 'diskon pendapatan') !== false
            ) {
                // For diskon pendapatan, make the nominal negative
                $p->nominal = -abs($p->nominal);
                $total_diskon_pendapatan += $p->nominal; // This will be negative
            } else if (
                $akun_name === 'diskon penjualan' ||
                strpos($akun_name, 'diskon penjualan') !== false
            ) {
                // For diskon penjualan, make the nominal negative
                $p->nominal = -abs($p->nominal);
                $total_diskon_penjualan += $p->nominal; // This will be negative
            }

            $total_pendapatan += $p->nominal;
            $pendapatan_processed[] = $p;
        }

        $total_beban = 0;
        foreach ($beban as $b) {
            $total_beban += $b->nominal;
        }

        if ($total_pendapatan > 0 || $total_beban > 0 || $this->hasSaldoAwal($pendapatan_raw) || $this->hasSaldoAwal($beban_raw)) {
            return response()->json([
                'status' => 200,
                'pendapatan' => $pendapatan_processed,
                'beban' => $beban,
                'total_pendapatan' => $total_pendapatan,
                'total_diskon_pendapatan' => $total_diskon_pendapatan,
                'total_diskon_penjualan' => $total_diskon_penjualan,
                'total_diskon' => $total_diskon_pendapatan + $total_diskon_penjualan,
                'total_beban' => $total_beban,
                'laba_rugi' => $total_pendapatan - $total_beban,
                'detail_pendapatan' => $pendapatan_raw, // Data detail untuk debugging
                'detail_beban' => $beban_raw // Data detail untuk debugging
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function kartustok()
    {
        return view('laporan.kartustok', ['title' => 'Laporan Kartu Stok']);
    }

    public function viewdatakartustok($periode)
    {
        $kartustok = Jurnal::viewkartustok($periode);
        if (count($kartustok) > 0) {
            return response()->json([
                'status' => 200,
                'kartustok' => $kartustok,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    public function laporanfifo()
    {
        return view('laporan.laporanfifo', ['title' => 'Laporan FIFO Stok']);
    }

    public function viewdatalaporanfifo($periode)
    {
        $data = Jurnal::viewlaporanfifo($periode);

        if (count($data) > 0) {
            return response()->json([
                'status' => 200,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.'
            ]);
        }
    }


    public function pengeluarankas()
    {
        return view('laporan.pengeluarankas', ['title' => 'Laporan Pengeluaran Kas']);
    }

    public function viewdatapengeluarankas($periode)
    {
        $pengeluarankas = Jurnal::viewpengeluarankas($periode);
        if (count($pengeluarankas) > 0) {
            return response()->json([
                'status' => 200,
                'pengeluarankas' => $pengeluarankas,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan.',
            ]);
        }
    }

    // Tambahkan method helper untuk mengecek apakah ada saldo awal
    private function hasSaldoAwal($akun_data)
    {
        foreach ($akun_data as $akun) {
            if ($akun->saldo_awal > 0) {
                return true;
            }
        }
        return false;
    }
}
