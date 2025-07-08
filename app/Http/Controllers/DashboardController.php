<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('year', date('Y'));
        $bulan = $request->input('month', date('m'));

        // Penjualan Bulanan
        $Penjualan = DB::select("
            SELECT a.waktu, IFNULL(b.total, 0) as total
            FROM v_waktu a
            LEFT OUTER JOIN (
                SELECT DATE_FORMAT(y.tgl_penjualan, '%Y-%m') as waktu, SUM(x.kuantitas * x.harga_satuan) as total
                FROM penjualan_detail x
                LEFT JOIN penjualan_header y ON x.id_penjualan_header = y.id
                WHERE YEAR(y.tgl_penjualan) = ?
                GROUP BY DATE_FORMAT(y.tgl_penjualan, '%Y-%m')
            ) b ON a.waktu = b.waktu
        ", [$tahun]);

        // Pendapatan Bulanan (replacing pengeluaran)
        $pendapatan = DB::select("
            SELECT a.waktu, IFNULL(b.total, 0) as total
            FROM v_waktu a
            LEFT OUTER JOIN (
                SELECT DATE_FORMAT(y.tgl_pendapatan, '%Y-%m') as waktu, SUM(x.subtotal) as total
                FROM pendapatan_detail x
                LEFT JOIN pendapatan_header y ON x.id_pendapatan_header = y.id
                WHERE YEAR(y.tgl_pendapatan) = ?
                GROUP BY DATE_FORMAT(y.tgl_pendapatan, '%Y-%m')
            ) b ON a.waktu = b.waktu
        ", [$tahun]);

        // Penjualan Harian untuk bulan dan tahun tertentu
        $penjualanHarian = DB::select("
            SELECT 
                DATE(y.tgl_penjualan) AS tanggal,
                SUM(x.kuantitas * x.harga_satuan) AS total
            FROM penjualan_detail x
            LEFT JOIN penjualan_header y ON x.id_penjualan_header = y.id
            WHERE YEAR(y.tgl_penjualan) = ? AND MONTH(y.tgl_penjualan) = ?
            GROUP BY DATE(y.tgl_penjualan)
            ORDER BY tanggal ASC
        ", [$tahun, $bulan]);

        // Pendapatan Harian (replacing pengeluaranHarian)
        $pendapatanHarian = DB::select("
            SELECT 
                DATE(y.tgl_pendapatan) AS tanggal,
                SUM(x.subtotal) AS total
            FROM pendapatan_detail x
            LEFT JOIN pendapatan_header y ON x.id_pendapatan_header = y.id
            WHERE YEAR(y.tgl_pendapatan) = ? AND MONTH(y.tgl_pendapatan) = ?
            GROUP BY DATE(y.tgl_pendapatan)
            ORDER BY tanggal ASC
        ", [$tahun, $bulan]);
        
        return view('dashboard', [
            'result' => $Penjualan,
            'pendapatan' => $pendapatan, // Changed from pengeluaran to pendapatan
            'penjualanHarian' => $penjualanHarian,
            'pendapatanHarian' => $pendapatanHarian, // Changed from pengeluaranHarian to pendapatanHarian
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
}