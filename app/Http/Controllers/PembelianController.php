<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Pembeliandetail;
use App\Models\Pembelianheader;
use App\Models\Persediaan;
use App\Models\Vendor;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembelian = DB::table('pembelian_header as a')
            ->leftJoin('vendor as b', 'a.id_vendor', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT id_pembelian_header, GROUP_CONCAT(' ',CONCAT(x.kuantitas, ' ', y.nama_barang)) as daftar_barang, SUM(x.kuantitas * x.harga_satuan) as total FROM pembelian_detail as x LEFT JOIN barang as y ON x.id_barang = y.id GROUP BY id_pembelian_header) as c"), 'a.id', '=', 'c.id_pembelian_header')
            ->select('a.id', 'a.status', 'a.no_pembelian', 'b.nama_vendor', 'c.daftar_barang', DB::raw('IFNULL(c.total, 0) as total'))
            ->get();

        return view('pembelian.index', ['pembelian' => $pembelian, 'title' => 'Pembelian Produk']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $no_pembelian = Autocode::code('pembelian_header', 'no_pembelian', 'PB');

        $vendor = Vendor::all();

        return view('pembelian.create', ['no_pembelian' => $no_pembelian, 'vendor' => $vendor, 'title' => 'Tambah Pembelian Produk']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pembelian' => 'required',
            'tgl_pembelian' => 'required',
            'keterangan' => 'required',
            'id_vendor' => 'required',
        ], [
            'no_pembelian.required' => 'Nomor Pembelian harus diisi',
            'tgl_pembelian.required' => 'Tanggal Pembelian harus diisi',
            'keterangan.required' => 'Keterangan harus diisi',
            'id_vendor.required' => 'Vendor harus diisi',
        ]);

        try {
            $pembelian = Pembelianheader::create([
                'no_pembelian' => $request->no_pembelian,
                'tgl_pembelian' => $request->tgl_pembelian,
                'keterangan' => $request->keterangan,
                'id_vendor' => $request->id_vendor,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('pembelian.detail', $pembelian->id)->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('pembelian.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pembelian = Pembelianheader::find($id);
        $vendor = Vendor::all();

        return view('pembelian.edit', ['pembelian' => $pembelian, 'vendor' => $vendor, 'title' => 'Edit Pembelian Produk']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tgl_pembelian' => 'required',
            'keterangan' => 'required',
            'id_vendor' => 'required',
        ], [
            'tgl_pembelian.required' => 'Tanggal Pembelian harus diisi',
            'keterangan.required' => 'Keterangan harus diisi',
            'id_vendor.required' => 'Vendor harus diisi',
        ]);

        try {
            Pembelianheader::where('id', $id)->update([
                'tgl_pembelian' => $request->tgl_pembelian,
                'keterangan' => $request->keterangan,
                'id_vendor' => $request->id_vendor,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('pembelian.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('pembelian.index')->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Pembelianheader::destroy($id);

            return redirect()->route('pembelian.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pembelian.index')->with('error', 'Data gagal dihapus');
        }
    }

    public function detail(string $id)
    {
        $pembelian = DB::table('pembelian_header as a')
            ->leftJoin('vendor as b', 'a.id_vendor', '=', 'b.id')
            ->select('a.id', 'a.no_pembelian', 'a.tgl_pembelian', 'a.keterangan', 'b.nama_vendor', 'a.id_vendor')
            ->where('a.id', $id)
            ->first();

        // Pastikan vendor tersedia
        if (!$pembelian || !$pembelian->id_vendor) {
            return redirect()->route('pembelian.index')->with('error', 'Vendor tidak ditemukan');
        }

        // Data detail pembelian
        $pembelian_detail = DB::table('pembelian_detail as a')
            ->leftJoin('barang as b', 'a.id_barang', '=', 'b.id')
            ->leftJoin('barang_vendor as c', function ($join) use ($pembelian) {
                $join->on('a.id_barang', '=', 'c.barang_id')
                    ->where('c.vendor_id', $pembelian->id_vendor);
            })
            ->select('a.id', 'a.kuantitas', 'a.harga_satuan', 'b.nama_barang', 'c.harga_satuan as harga_vendor')
            ->where('a.id_pembelian_header', $id)
            ->get();

        // Data barang yang tersedia untuk vendor ini
        $barang = DB::table('barang as a')
            ->join('barang_vendor as b', 'a.id', '=', 'b.barang_id')
            ->where('b.vendor_id', $pembelian->id_vendor)
            ->select('a.id', 'a.nama_barang', 'b.harga_satuan')
            ->get()
            ->map(function ($item) {
                $item->harga_formatted = number_format($item->harga_satuan, 0, ',', '.');
                return $item;
            });

        return view('pembelian.detail', [
            'pembelian' => $pembelian,
            'pembelian_detail' => $pembelian_detail,
            'barang' => $barang,
            'title' => 'Detail Pembelian Produk'
        ]);
    }

    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'kuantitas' => 'required|min:1',
            'id_barang' => 'required',
            'id_pembelian_header' => 'required',
        ]);

        try {
            $header = Pembelianheader::find($request->id_pembelian_header);

            // Ambil harga dari barang_vendor
            $barangVendor = DB::table('barang_vendor')
                ->where('barang_id', $request->id_barang)
                ->where('vendor_id', $header->id_vendor)
                ->first();

            if (!$barangVendor) {
                return redirect()->back()->with('error', 'Harga barang untuk vendor ini tidak ditemukan');
            }

            $pembelian_detail = Pembeliandetail::create([
                'kuantitas' => $request->kuantitas,
                'harga_satuan' => $barangVendor->harga_satuan, // Gunakan harga dari barang_vendor
                'id_barang' => $request->id_barang,
                'id_pembelian_header' => $request->id_pembelian_header,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            Persediaan::create([
                'tgl_persediaan' => $header->tgl_pembelian,
                'keterangan' => 'Pembelian Barang dengan Nomor Pembelian ' . $request->no_pembelian,
                'kuantitas' => $request->kuantitas,
                'harga_beli_satuan' => $barangVendor->harga_satuan,
                'harga_jual_satuan' => $barangVendor->harga_jual,
                'id_barang' => $request->id_barang,
                'id_pembelian_detail' => $pembelian_detail->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            $check_akun_kas = Coa::where('kode_akun', '101')->first();
            $check_akun_pembelian = Coa::where('kode_akun', '106')->first();

            if (!$check_akun_kas) {
                $coa_kas = Coa::create([
                    'kode_akun' => '101',
                    'nama_akun' => 'Kas',
                    'header_akun' => 1,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            if (!$check_akun_pembelian) {
                $coa_pembelian = Coa::create([
                    'kode_akun' => '106',
                    'nama_akun' => 'Pembelian',
                    'header_akun' => 1,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            $jurnal_debit = Jurnal::create([
                'no_jurnal' => Autocode::code('jurnal', 'no_jurnal', 'JU'),
                'tgl_jurnal' => $header->tgl_pembelian,
                'posisi_dr_cr' => 'd',
                'nominal' => $request->kuantitas * $request->harga_satuan,
                'jenis_transaksi' => 'pembelian',
                'id_transaksi' => $pembelian_detail->id,
                'id_coa' => !$check_akun_pembelian ? $coa_pembelian->id : $check_akun_pembelian->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            Jurnal::create([
                'no_jurnal' => $jurnal_debit->no_jurnal,
                'tgl_jurnal' => $header->tgl_pembelian,
                'posisi_dr_cr' => 'c',
                'nominal' => $request->kuantitas * $request->harga_satuan,
                'jenis_transaksi' => 'pembelian',
                'id_transaksi' => $pembelian_detail->id,
                'id_coa' => !$check_akun_kas ? $coa_kas->id : $check_akun_kas->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('pembelian.detail', $request->id_pembelian_header)->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('pembelian.detail', $request->id_pembelian_header)->with('error', 'Data gagal disimpan');
        }
    }

    public function destroydetail(string $id)
    {
        $pembelian_detail = Pembeliandetail::find($id);

        try {
            Persediaan::where('id_pembelian_detail', $id)->delete();
            Jurnal::where('jenis_transaksi', 'pembelian')->where('id_transaksi', $id)->delete();

            $pembelian_detail->delete();

            return redirect()->route('pembelian.detail', $pembelian_detail->id_pembelian_header)->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pembelian.detail', $pembelian_detail->id_pembelian_header)->with('error', 'Data gagal dihapus');
        }
    }

    public function finish($id)
    {
        $nominal = DB::table('pembelian_detail')->where('id_pembelian_header', $id)->sum(DB::raw('kuantitas * harga_satuan'));
        if ($nominal == 0) {
            return redirect()->route('pembelian.index')->with('error', 'Pembelian gagal diselesaikan');
        }
        try {
            Pembelianheader::where('id', $id)->update([
                'status' => 'finished',
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diselesaikan');
        } catch (\Exception $e) {
            return redirect()->route('pembelian.index')->with('error', 'Pembelian gagal diselesaikan');
        }
    }
}
