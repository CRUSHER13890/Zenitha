<?php
// transaksi penjualan
namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Pelanggan;
use App\Models\Pengambilan;
use App\Models\Penjualandetail;
use App\Models\Penjualanheader;
use App\Models\Diskon;
use App\Models\Persediaan;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = DB::table('penjualan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT x.id_penjualan_header, GROUP_CONCAT(' ',CONCAT(x.kuantitas, ' ', y.nama_barang)) as daftar_barang, SUM(x.subtotal) as total FROM penjualan_detail as x LEFT JOIN barang as y ON x.id_barang = y.id GROUP BY x.id_penjualan_header) as c"), 'a.id', '=', 'c.id_penjualan_header')
            ->select('a.id', 'a.no_penjualan', 'b.nama_pelanggan', 'c.daftar_barang', 'a.status_pembayaran', DB::raw('IFNULL(c.total, 0) as total'))
            ->get();

        return view('penjualan.index', ['penjualan' => $penjualan, 'title' => 'Penjualan Produk']);
    }

    public function create()
    {
        $no_penjualan = Autocode::code('penjualan_header', 'no_penjualan', 'PP');
        $pelanggan = Pelanggan::all();

        return view('penjualan.create', ['no_penjualan' => $no_penjualan, 'pelanggan' => $pelanggan, 'title' => 'Tambah Penjualan Produk']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_penjualan' => 'required',
            'tgl_penjualan' => 'required',
            'keterangan' => 'required',
            'id_pelanggan' => 'required',
        ], [
            'no_penjualan.required' => 'Nomor Penjualan harus diisi',
            'tgl_penjualan.required' => 'Tanggal Penjualan harus diisi',
            'keterangan.required' => 'Keterangan harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
        ]);

        try {
            $penjualan = Penjualanheader::create([
                'no_penjualan' => $request->no_penjualan,
                'tgl_penjualan' => $request->tgl_penjualan,
                'keterangan' => $request->keterangan,
                'status_pembayaran' => 'lunas',
                'id_pelanggan' => $request->id_pelanggan,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('penjualan.detail', $penjualan->id)->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.index')->with('error', 'Data gagal disimpan');
        }
    }

    public function edit(string $id)
    {
        $penjualan = Penjualanheader::find($id);
        $pelanggan = Pelanggan::all();

        return view('penjualan.edit', ['penjualan' => $penjualan, 'pelanggan' => $pelanggan, 'title' => 'Edit Penjualan Produk']);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tgl_penjualan' => 'required',
            'keterangan' => 'required',
            'id_pelanggan' => 'required',
        ], [
            'tgl_penjualan.required' => 'Tanggal Penjualan harus diisi',
            'keterangan.required' => 'Keterangan harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
        ]);

        try {
            Penjualanheader::where('id', $id)->update([
                'tgl_penjualan' => $request->tgl_penjualan,
                'keterangan' => $request->keterangan,
                'id_pelanggan' => $request->id_pelanggan,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('penjualan.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.index')->with('error', 'Data gagal diubah');
        }
    }

    public function destroy(string $id)
    {
        try {
            Penjualanheader::destroy($id);

            return redirect()->route('penjualan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.index')->with('error', 'Data gagal dihapus');
        }
    }

    public function detail($id)
    {
        $penjualan = DB::table('penjualan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->select('a.id', 'a.no_penjualan', 'a.tgl_penjualan', 'a.keterangan', 'b.nama_pelanggan')
            ->where('a.id', $id)
            ->first();

        $penjualan_detail = DB::table('penjualan_detail as a')
            ->leftJoin('barang as b', 'a.id_barang', '=', 'b.id')
            ->leftJoin('diskon as d', 'a.id_diskon', '=', 'd.id')
            ->select('a.id', 'a.kuantitas', 'b.nama_barang', 'a.harga_satuan', 'a.diskon', 'a.subtotal', 'd.nama_diskon', 'd.persentase_diskon')
            ->where('a.id_penjualan_header', $id)
            ->get();

        // Get available items with FIFO - MENGGUNAKAN HARGA DARI PERSEDIAAN
        $barang = DB::table('persediaan as a')
            ->leftJoin('barang as b', 'a.id_barang', '=', 'b.id')
            ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as kuantitas FROM pengambilan GROUP BY id_persediaan) as c'), 'a.id', '=', 'c.id_persediaan')
            ->select(
                'a.id as id_persediaan',
                'a.id_barang',
                'b.nama_barang',
                'a.harga_jual_satuan', // Menggunakan harga dari persediaan
                'a.tgl_persediaan',
                DB::raw('a.kuantitas - IFNULL(c.kuantitas, 0) as stok_tersedia')
            )
            ->whereRaw('a.kuantitas - IFNULL(c.kuantitas, 0) > 0')
            ->orderBy('a.id_barang')
            ->orderBy('a.tgl_persediaan') // FIFO berdasarkan tanggal
            ->get()
            ->groupBy('id_barang')
            ->map(function ($group) {
                // Untuk setiap barang, ambil persediaan pertama (FIFO) yang masih ada stok
                $firstAvailable = $group->first();
                $totalStok = $group->sum('stok_tersedia');

                return (object) [
                    'id_persediaan' => $firstAvailable->id_persediaan,
                    'id_barang' => $firstAvailable->id_barang,
                    'nama_barang' => $firstAvailable->nama_barang,
                    'harga_jual_satuan' => $firstAvailable->harga_jual_satuan,
                    'stok' => $totalStok,
                    'tgl_persediaan' => $firstAvailable->tgl_persediaan
                ];
            });

        return view('penjualan.detail', [
            'penjualan' => $penjualan,
            'penjualan_detail' => $penjualan_detail,
            'barang' => $barang,
            'title' => 'Detail Penjualan Produk'
        ]);
    }

    public function getDiskon(Request $request)
    {
        $id_barang = $request->id_barang;
        $total = $request->total;
        $tgl_penjualan = $request->tgl_penjualan ?? now()->format('Y-m-d');

        $query = Diskon::where('id_barang', $id_barang)
            ->where('min_transaksi', '<=', $total);

        $query->where(function ($q) use ($tgl_penjualan) {
            $q->whereNull('tanggal_mulai')
                ->orWhere(function ($q2) use ($tgl_penjualan) {
                    $q2->where('tanggal_mulai', '<=', $tgl_penjualan)
                        ->where('tanggal_selesai', '>=', $tgl_penjualan);
                });
        });

        $diskon = $query->orderBy('max_diskon', 'desc')
            ->first();

        if ($diskon) {
            $diskon_nominal = min(($total * $diskon->persentase_diskon / 100), $diskon->max_diskon);
            return response()->json([
                'success' => true,
                'diskon' => $diskon,
                'diskon_nominal' => $diskon_nominal,
                'subtotal' => $total - $diskon_nominal
            ]);
        }

        return response()->json([
            'success' => false,
            'diskon_nominal' => 0,
            'subtotal' => $total
        ]);
    }


    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'kuantitas' => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'id_barang' => 'required|exists:barang,id',
            'id_penjualan_header' => 'required|exists:penjualan_header,id',
        ], [
            'kuantitas.required' => 'Kuantitas harus diisi',
            'kuantitas.min' => 'Kuantitas minimal 1',
            'harga_satuan.required' => 'Harga Satuan harus diisi',
            'id_barang.required' => 'Barang harus diisi',
            'id_penjualan_header.required' => 'Header Penjualan harus valid',
        ]);

        try {
            $penjualan_header = Penjualanheader::find($request->id_penjualan_header);

            // FIFO inventory check - MENGGUNAKAN HARGA DARI PERSEDIAAN
            $persediaan = DB::table('persediaan as a')
                ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as kuantitas FROM pengambilan GROUP BY id_persediaan) as b'), 'a.id', '=', 'b.id_persediaan')
                ->select(
                    'a.id',
                    'a.id_barang',
                    'a.harga_jual_satuan',
                    'a.harga_beli_satuan',
                    'a.tgl_persediaan',
                    DB::raw('a.kuantitas - COALESCE(b.kuantitas, 0) as saldo_stok')
                )
                ->where('a.id_barang', $request->id_barang)
                ->whereRaw('a.kuantitas - COALESCE(b.kuantitas, 0) > 0')
                ->orderBy('a.tgl_persediaan') // FIFO berdasarkan tanggal
                ->orderBy('a.id') // Jika tanggal sama, berdasarkan ID
                ->get();

            $total_stok = $persediaan->sum('saldo_stok');
            $kuantitas_penjualan = $request->kuantitas;

            if ($kuantitas_penjualan > $total_stok) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $total_stok);
            }

            // Validasi harga satuan harus sesuai dengan persediaan FIFO pertama
            $persediaan_pertama = $persediaan->first();
            if ($request->harga_satuan != $persediaan_pertama->harga_jual_satuan) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Harga satuan tidak sesuai dengan harga FIFO. Harga yang benar: Rp. ' . number_format($persediaan_pertama->harga_jual_satuan, 0, ',', '.'));
            }

            // Hitung total dan diskon berdasarkan WEIGHTED AVERAGE dari persediaan yang akan diambil
            $total_harga = 0;
            $kuantitas_temp = $kuantitas_penjualan;

            foreach ($persediaan as $item) {
                if ($kuantitas_temp <= 0) break;

                $ambil = min($item->saldo_stok, $kuantitas_temp);
                $total_harga += $ambil * $item->harga_jual_satuan;
                $kuantitas_temp -= $ambil;
            }

            $harga_rata_rata = $total_harga / $kuantitas_penjualan;


            // Check diskon berdasarkan total harga rata-rata
            $total = $kuantitas_penjualan * $harga_rata_rata;

            $diskon = Diskon::where('id_barang', $request->id_barang)
                ->where('min_transaksi', '<=', $total)
                ->where(function ($q) use ($penjualan_header) {
                    $q->whereNull('tanggal_mulai')
                        ->orWhere(function ($q2) use ($penjualan_header) {
                            $q2->where('tanggal_mulai', '<=', $penjualan_header->tgl_penjualan)
                                ->where('tanggal_selesai', '>=', $penjualan_header->tgl_penjualan);
                        });
                })
                ->orderBy('max_diskon', 'desc')
                ->first();

            $diskon_nominal = 0;
            $id_diskon = null;

            if ($diskon) {
                $diskon_nominal = min(($total * $diskon->persentase_diskon / 100), $diskon->max_diskon);
                $id_diskon = $diskon->id;
            }

            $subtotal = $total - $diskon_nominal;

            // Create penjualan detail
            $penjualan_detail = Penjualandetail::create([
                'kuantitas' => $kuantitas_penjualan,
                'harga_satuan' => $harga_rata_rata, // Menggunakan harga rata-rata dari FIFO
                'diskon' => $diskon_nominal,
                'subtotal' => $subtotal,
                'id_barang' => $request->id_barang,
                'id_diskon' => $id_diskon,
                'id_penjualan_header' => $request->id_penjualan_header,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            // Process FIFO inventory
            $kuantitas_sisa = $kuantitas_penjualan;
            foreach ($persediaan as $item) {
                if ($kuantitas_sisa <= 0) break;

                $ambil = min($item->saldo_stok, $kuantitas_sisa);

                Pengambilan::create([
                    'tgl_pengambilan' => $penjualan_header->tgl_penjualan,
                    'keterangan' => 'Penjualan ' . $penjualan_header->no_penjualan,
                    'kuantitas' => $ambil,
                    'id_barang' => $request->id_barang,
                    'id_persediaan' => $item->id,
                    'id_penjualan_detail' => $penjualan_detail->id,
                    'harga_satuan' => $item->harga_jual_satuan,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);

                $kuantitas_sisa -= $ambil;
            }

            // Create journal entries
            $this->createJournalEntries($penjualan_header, $penjualan_detail, $subtotal, $diskon_nominal, $total);

            return redirect()->route('penjualan.detail', $request->id_penjualan_header)
                ->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.detail', $request->id_penjualan_header)
                ->with('error', 'Data gagal disimpan: ' . $e->getMessage());
        }
    }

    private function createJournalEntries($penjualan_header, $penjualan_detail, $subtotal, $diskon_nominal, $total)
    {
        // Journal entries logic
        $check_akun_kas = Coa::where('kode_akun', '101')->first();
        $check_akun_diskon_penjualan = Coa::where('kode_akun', '411')->first();
        $check_akun_penjualan = Coa::where('kode_akun', '406')->first();
        $check_akun_hpp = Coa::where('kode_akun', '511')->first();
        $check_akun_pbd = Coa::where('kode_akun', '106')->first();

        if (!$check_akun_kas) {
            $coa_kas = Coa::create([
                'kode_akun' => '101',
                'nama_akun' => 'Kas',
                'header_akun' => 1,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);
        }

        if (!$check_akun_diskon_penjualan) {
            $coa_diskon_penjualan = Coa::create([
                'kode_akun' => '411',
                'nama_akun' => 'Diskon Penjualan',
                'header_akun' => 4,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);
        }

        if (!$check_akun_penjualan) {
            $coa_penjualan = Coa::create([
                'kode_akun' => '406',
                'nama_akun' => 'Penjualan',
                'header_akun' => 4,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);
        }

        if (!$check_akun_pbd) {
            $coa_pbd = Coa::create([
                'kode_akun' => '106',
                'nama_akun' => 'Persediaan Barang Dagang',
                'header_akun' => 1,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);
        }

        if (!$check_akun_hpp) {
            $coa_hpp = Coa::create([
                'kode_akun' => '511',
                'nama_akun' => 'Harga Pokok Penjualan',
                'header_akun' => 5,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);
        }

        // Hitung total HPP berdasarkan harga beli dari pengambilan FIFO
        $total_hpp = DB::table('pengambilan as p')
            ->join('persediaan as ps', 'p.id_persediaan', '=', 'ps.id')
            ->where('p.id_penjualan_detail', $penjualan_detail->id)
            ->sum(DB::raw('p.kuantitas * ps.harga_beli_satuan'));

        $jurnal_debit = Jurnal::create([
            'no_jurnal' => Autocode::code('jurnal', 'no_jurnal', 'JU'),
            'tgl_jurnal' => $penjualan_header->tgl_penjualan,
            'posisi_dr_cr' => 'd',
            'nominal' => $subtotal,
            'jenis_transaksi' => 'penjualan',
            'id_transaksi' => $penjualan_detail->id,
            'id_coa' => $check_akun_kas ? $check_akun_kas->id : $coa_kas->id,
            'user_id_created' => Auth::user()->id,
            'user_id_updated' => Auth::user()->id,
        ]);

        if ($diskon_nominal > 0) {
            Jurnal::create([
                'no_jurnal' => $jurnal_debit->no_jurnal,
                'tgl_jurnal' => $penjualan_header->tgl_penjualan,
                'posisi_dr_cr' => 'd',
                'nominal' => $diskon_nominal,
                'jenis_transaksi' => 'penjualan',
                'id_transaksi' => $penjualan_detail->id,
                'id_coa' => $check_akun_diskon_penjualan ? $check_akun_diskon_penjualan->id : $coa_diskon_penjualan->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);
        }

        Jurnal::create([
            'no_jurnal' => $jurnal_debit->no_jurnal,
            'tgl_jurnal' => $penjualan_header->tgl_penjualan,
            'posisi_dr_cr' => 'c',
            'nominal' => $total,
            'jenis_transaksi' => 'penjualan',
            'id_transaksi' => $penjualan_detail->id,
            'id_coa' => $check_akun_penjualan ? $check_akun_penjualan->id : $coa_penjualan->id,
            'user_id_created' => Auth::user()->id,
            'user_id_updated' => Auth::user()->id,
        ]);

        // HPP entry (Debit)
        Jurnal::create([
            'no_jurnal' => $jurnal_debit->no_jurnal,
            'tgl_jurnal' => $penjualan_header->tgl_penjualan,
            'posisi_dr_cr' => 'd',
            'nominal' => $total_hpp,
            'jenis_transaksi' => 'penjualan',
            'id_transaksi' => $penjualan_detail->id,
            'id_coa' => $check_akun_hpp ? $check_akun_hpp->id : $coa_hpp->id,
            'user_id_created' => Auth::user()->id,
            'user_id_updated' => Auth::user()->id,
        ]);

        // Persediaan Barang Dagang entry (Credit)
        Jurnal::create([
            'no_jurnal' => $jurnal_debit->no_jurnal,
            'tgl_jurnal' => $penjualan_header->tgl_penjualan,
            'posisi_dr_cr' => 'c',
            'nominal' => $total_hpp,
            'jenis_transaksi' => 'penjualan',
            'id_transaksi' => $penjualan_detail->id,
            'id_coa' => $check_akun_pbd ? $check_akun_pbd->id : $coa_pbd->id,
            'user_id_created' => Auth::user()->id,
            'user_id_updated' => Auth::user()->id,
        ]);
    }

    // Method untuk mendapatkan data barang dengan harga FIFO
    public function getBarangFifo(Request $request)
    {
        $id_barang = $request->id_barang;

        $persediaan_fifo = DB::table('persediaan as a')
            ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as kuantitas FROM pengambilan GROUP BY id_persediaan) as b'), 'a.id', '=', 'b.id_persediaan')
            ->select(
                'a.id',
                'a.harga_jual_satuan',
                'a.tgl_persediaan',
                DB::raw('a.kuantitas - IFNULL(b.kuantitas, 0) as stok_tersedia')
            )
            ->where('a.id_barang', $id_barang)
            ->whereRaw('a.kuantitas - IFNULL(b.kuantitas, 0) > 0')
            ->orderBy('a.tgl_persediaan')
            ->orderBy('a.id')
            ->first();

        if ($persediaan_fifo) {
            return response()->json([
                'success' => true,
                'harga_fifo' => $persediaan_fifo->harga_jual_satuan,
                'stok_tersedia' => $persediaan_fifo->stok_tersedia,
                'tanggal_persediaan' => $persediaan_fifo->tgl_persediaan
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada stok tersedia untuk barang ini'
        ]);
    }

    public function destroydetail($id)
    {
        $penjualan_detail = Penjualandetail::find($id);

        try {
            Pengambilan::where('id_penjualan_detail', $id)->delete();
            Jurnal::where('jenis_transaksi', 'penjualan')->where('id_transaksi', $id)->delete();

            $penjualan_detail->delete();

            return redirect()->route('penjualan.detail', $penjualan_detail->id_penjualan_header)
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.detail', $penjualan_detail->id_penjualan_header)
                ->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        }
    }
}
