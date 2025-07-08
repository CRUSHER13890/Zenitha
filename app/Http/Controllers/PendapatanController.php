<?php
// pendapatan c
namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Pelanggan;
use App\Models\Pendapatandetail;
use App\Models\Pendapatanheader;
use App\Models\Diskon;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendapatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendapatan = DB::table('pendapatan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->select('a.id', 'a.no_pendapatan', 'a.tgl_pendapatan', 'b.nama_pelanggan')
            ->get();

        foreach ($pendapatan as $item) {
            $reservations = DB::table('pendapatan_detail as pd')
                ->join('reservasi as r', 'pd.id_reservasi', '=', 'r.id')
                ->where('pd.id_pendapatan_header', '=', $item->id)
                ->pluck('r.no_reservasi')
                ->toArray();

            $item->no_reservasi = !empty($reservations) ? implode(', ', $reservations) : '-';

            $total_subtotal = DB::table('pendapatan_detail')
                ->where('id_pendapatan_header', $item->id)
                ->sum('subtotal');

            $item->total_subtotal = $total_subtotal ?: 0;
        }

        return view('pendapatan.index', ['pendapatan' => $pendapatan, 'title' => 'Pendapatan Jasa']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $no_pendapatam = Autocode::code('pendapatan_header', 'no_pendapatan', 'PDT');
        $pelanggan = Pelanggan::all();

        return view('pendapatan.create', [
            'no_pendapatan' => $no_pendapatam,
            'pelanggan' => $pelanggan,
            'title' => 'Tambah Pendapatan Jasa'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pendapatan' => 'required',
            'tgl_pendapatan' => 'required',
            'id_pelanggan' => 'required',
        ], [
            'no_pendapatan.required' => 'Nomor Pendapatan harus diisi',
            'tgl_pendapatan.required' => 'Tanggal Pendapatan harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
        ]);

        try {
            $pendapatan = Pendapatanheader::create([
                'no_pendapatan' => $request->no_pendapatan,
                'tgl_pendapatan' => $request->tgl_pendapatan,
                'id_pelanggan' => $request->id_pelanggan,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('pendapatan.detail', $pendapatan->id)
                ->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('pendapatan.index')
                ->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pendapatan = Pendapatanheader::find($id);
        $pelanggan = Pelanggan::all();

        return view('pendapatan.edit', [
            'pendapatan' => $pendapatan,
            'pelanggan' => $pelanggan,
            'title' => 'Edit Pendapatan Jasa'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tgl_pendapatan' => 'required',
            'id_pelanggan' => 'required',
        ], [
            'tgl_pendapatan.required' => 'Tanggal Pendapatan harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
        ]);

        try {
            Pendapatanheader::where('id', $id)->update([
                'tgl_pendapatan' => $request->tgl_pendapatan,
                'id_pelanggan' => $request->id_pelanggan,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('pendapatan.index')
                ->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->route('pendapatan.index')
                ->with('error', 'Data gagal diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Pendapatanheader::destroy($id);
            return redirect()->route('pendapatan.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pendapatan.index')
                ->with('error', 'Data gagal dihapus');
        }
    }

    public function detail($id)
    {
        $pendapatan = DB::table('pendapatan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->select('a.id', 'a.no_pendapatan', 'a.tgl_pendapatan', 'b.nama_pelanggan', 'b.id as id_pelanggan')
            ->where('a.id', $id)
            ->first();

        $reservations = DB::table('reservasi as r')
            ->join('layanan as l', 'r.id_layanan', '=', 'l.id')
            ->leftJoin('pendapatan_detail as pd', 'r.id', '=', 'pd.id_reservasi')
            ->where('r.id_pelanggan', $pendapatan->id_pelanggan)
            ->where('r.status', '=', 'Disetujui')
            ->whereNull('pd.id_reservasi')
            ->select('r.id', 'r.no_reservasi', 'l.nama_layanan', 'r.ruangan', 'r.waktu_mulai', 'r.waktu_selesai', 'l.harga_layanan as harga', 'l.id as id_layanan')
            ->get();

        $pendapatan_detail = DB::table('pendapatan_detail as pd')
            ->leftJoin('reservasi as r', 'pd.id_reservasi', '=', 'r.id')
            ->leftJoin('layanan as l', 'pd.id_layanan', '=', 'l.id')
            ->where('pd.id_pendapatan_header', $id)
            ->select('pd.*', 'r.no_reservasi', 'l.nama_layanan')
            ->get();

        return view('pendapatan.detail', [
            'pendapatan' => $pendapatan,
            'pendapatan_detail' => $pendapatan_detail,
            'reservations' => $reservations,
            'title' => 'Detail Pendapatan Jasa',
        ]);
    }

    /**
     * Get available discount for service
     */
    public function getDiskonPendapatan(Request $request)
    {
        $request->validate([
            'id_layanan' => 'required|exists:layanan,id',
            'tgl_pendapatan' => 'nullable|date'
        ]);

        $diskon = Diskon::where('id_layanan', $request->id_layanan)
            ->where(function ($query) use ($request) {
                $query->whereNull('tanggal_mulai')
                    ->orWhere(function ($q) use ($request) {
                        $q->where('tanggal_mulai', '<=', $request->tgl_pendapatan)
                            ->where('tanggal_selesai', '>=', $request->tgl_pendapatan);
                    });
            })
            ->orderBy('persentase_diskon', 'desc')
            ->first();

        if ($diskon) {
            return response()->json([
                'success' => true,
                'diskon' => [
                    'id' => $diskon->id,
                    'nama_diskon' => $diskon->nama_diskon,
                    'persentase_diskon' => $diskon->persentase_diskon,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada diskon yang tersedia'
        ]);
    }

    /**
     * Store pendapatan detail
     */
    public function storedetail(Request $request)
    {
        $validated = $request->validate([
            'id_reservasi' => 'required|exists:reservasi,id',
            'harga' => 'required|numeric|min:0',
            'diskon' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'id_layanan' => 'required|exists:layanan,id',
            'id_pendapatan_header' => 'required|exists:pendapatan_header,id'
        ]);

        DB::beginTransaction();
        try {
            // Create pendapatan detail
            $pendapatan_detail = Pendapatandetail::create([
                'id_reservasi' => $request->id_reservasi,
                'harga' => $request->harga,
                'diskon' => $request->diskon,
                'subtotal' => $request->subtotal,
                'id_layanan' => $request->id_layanan,
                'id_pendapatan_header' => $request->id_pendapatan_header,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            // Update reservation status
            DB::table('reservasi')
                ->where('id', $request->id_reservasi)
                ->update([
                    'status' => 'Selesai',
                    'updated_at' => now()
                ]);

            // Create journal entries
            $this->createJournalEntries($pendapatan_detail);

            DB::commit();
            return redirect()->route('pendapatan.detail', $request->id_pendapatan_header)
                ->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pendapatan.detail', $request->id_pendapatan_header)
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Create journal entries for pendapatan
     */
    protected function createJournalEntries($pendapatan_detail)
    {
        // Get the pendapatan header to access tgl_pendapatan
        $pendapatan_header = Pendapatanheader::find($pendapatan_detail->id_pendapatan_header);

        $check_akun_kas = Coa::firstOrCreate(
            ['kode_akun' => '101'],
            [
                'nama_akun' => 'Kas',
                'header_akun' => 1,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id
            ]
        );

        $check_akun_diskon = Coa::firstOrCreate(
            ['kode_akun' => '412'],
            [
                'nama_akun' => 'Diskon Pendapatan',
                'header_akun' => 4,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id
            ]
        );

        $check_akun_pendapatan = Coa::firstOrCreate(
            ['kode_akun' => '401'],
            [
                'nama_akun' => 'Pendapatan',
                'header_akun' => 4,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id
            ]
        );

        $no_jurnal = Autocode::code('jurnal', 'no_jurnal', 'JU');

        // Debit Kas
        Jurnal::create([
            'no_jurnal' => $no_jurnal,
            'tgl_jurnal' => $pendapatan_header->tgl_pendapatan,
            'posisi_dr_cr' => 'd',
            'nominal' => $pendapatan_detail->subtotal,
            'jenis_transaksi' => 'pendapatan',
            'id_transaksi' => $pendapatan_detail->id,
            'id_coa' => $check_akun_kas->id,
            'user_id_created' => Auth::user()->id,
            'user_id_updated' => Auth::user()->id,
        ]);

        // Debit Diskon (if any)
        if ($pendapatan_detail->diskon > 0) {
            Jurnal::create([
                'no_jurnal' => $no_jurnal,
                'tgl_jurnal' => $pendapatan_header->tgl_pendapatan,
                'posisi_dr_cr' => 'd',
                'nominal' => $pendapatan_detail->diskon,
                'jenis_transaksi' => 'pendapatan',
                'id_transaksi' => $pendapatan_detail->id,
                'id_coa' => $check_akun_diskon->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);
        }

        // Credit Pendapatan
        Jurnal::create([
            'no_jurnal' => $no_jurnal,
            'tgl_jurnal' => $pendapatan_header->tgl_pendapatan,
            'posisi_dr_cr' => 'c',
            'nominal' => $pendapatan_detail->harga,
            'jenis_transaksi' => 'pendapatan',
            'id_transaksi' => $pendapatan_detail->id,
            'id_coa' => $check_akun_pendapatan->id,
            'user_id_created' => Auth::user()->id,
            'user_id_updated' => Auth::user()->id,
        ]);
    }

    /**
     * Remove pendapatan detail
     */
    public function destroydetail($id)
    {
        $pendapatan_detail = Pendapatandetail::findOrFail($id);
        $pendapatan_header_id = $pendapatan_detail->id_pendapatan_header;

        DB::beginTransaction();
        try {
            // Update reservation status back
            DB::table('reservasi')
                ->where('id', $pendapatan_detail->id_reservasi)
                ->update(['status' => 'Disetujui']);

            // Delete journal entries
            Jurnal::where('jenis_transaksi', 'pendapatan')
                ->where('id_transaksi', $id)
                ->delete();

            // Delete detail
            $pendapatan_detail->delete();

            DB::commit();
            return redirect()->route('pendapatan.detail', $pendapatan_header_id)
                ->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pendapatan.detail', $pendapatan_header_id)
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
