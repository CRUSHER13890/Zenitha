<?php
// pengeluaran
namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Jurnal;
use App\Models\Pengeluaran;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluaran = Pengeluaran::all();
        return view('pengeluaran.index', ['pengeluaran' => $pengeluaran, 'title' => 'Pengeluaran']);
    }

    public function create()
    {
        $no_pengeluaran = Autocode::code('pengeluaran', 'no_pengeluaran', 'POR');
        return view('pengeluaran.create', ['no_pengeluaran' => $no_pengeluaran, 'title' => 'Tambah Pengeluaran']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pengeluaran' => 'required',
            'tgl_pengeluaran' => 'required',
            'nominal' => 'required',
            'tipe_pengeluaran' => 'required',
        ], [
            'no_pengeluaran.required' => 'Nomor Pengeluaran harus diisi',
            'tgl_pengeluaran.required' => 'Tanggal Pengeluaran harus diisi',
            'nominal.required' => 'Nominal harus diisi',
            'tipe_pengeluaran.required' => 'Tipe Pengeluaran harus diisi',
        ]);

        try {
            $pengeluaran = Pengeluaran::create([
                'no_pengeluaran' => $request->no_pengeluaran,
                'tgl_pengeluaran' => $request->tgl_pengeluaran,
                'nominal' => $request->nominal,
                'tipe_pengeluaran' => $request->tipe_pengeluaran,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            $beban = $request->tipe_pengeluaran == 'Listrik' ? '501' :
                     ($request->tipe_pengeluaran == 'Sewa' ? '502' :
                     ($request->tipe_pengeluaran == 'Air' ? '503' :
                     ($request->tipe_pengeluaran == 'Wifi' ? '504' : '505')));

            $check_akun_kas = Coa::where('kode_akun', '101')->first();
            $check_akun_pengeluaran = Coa::where('kode_akun', $beban)->first();

            if (!$check_akun_kas) {
                $coa_kas = Coa::create([
                    'kode_akun' => '101',
                    'nama_akun' => 'Kas',
                    'header_akun' => '1',
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            if (!$check_akun_pengeluaran) {
                $coa_pengeluaran = Coa::create([
                    'kode_akun' => $beban,
                    'nama_akun' => "Beban $request->tipe_pengeluaran",
                    'header_akun' => 5,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            $jurnal_debit = Jurnal::create([
                'no_jurnal' => Autocode::code('jurnal', 'no_jurnal', 'JU'),
                'tgl_jurnal' => $request->tgl_pengeluaran,
                'posisi_dr_cr' => 'd',
                'nominal' => $request->nominal,
                'jenis_transaksi' => 'pengeluaran',
                'id_transaksi' => $pengeluaran->id,
                'id_coa' => !$check_akun_pengeluaran ? $coa_pengeluaran->id : $check_akun_pengeluaran->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            Jurnal::create([
                'no_jurnal' => $jurnal_debit->no_jurnal,
                'tgl_jurnal' => $request->tgl_pengeluaran,
                'posisi_dr_cr' => 'c',
                'nominal' => $request->nominal,
                'jenis_transaksi' => 'pengeluaran',
                'id_transaksi' => $pengeluaran->id,
                'id_coa' => !$check_akun_kas ? $coa_kas->id : $check_akun_kas->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('pengeluaran.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')->with('error', 'Data gagal disimpan');
        }
    }

    public function edit(string $id)
    {
        $pengeluaran = Pengeluaran::find($id);
        return view('pengeluaran.edit', ['pengeluaran' => $pengeluaran, 'title' => 'Edit Pengeluaran']);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'no_pengeluaran' => 'required',
            'tgl_pengeluaran' => 'required',
            'nominal' => 'required',
            'tipe_pengeluaran' => 'required',
        ], [
            'no_pengeluaran.required' => 'Nomor Pengeluaran harus diisi',
            'tgl_pengeluaran.required' => 'Tanggal Pengeluaran harus diisi',
            'nominal.required' => 'Nominal harus diisi',
            'tipe_pengeluaran.required' => 'Tipe Pengeluaran harus diisi',
        ]);

        try {
            Pengeluaran::where('id', $id)->update([
                'no_pengeluaran' => $request->no_pengeluaran,
                'tgl_pengeluaran' => $request->tgl_pengeluaran,
                'tipe_pengeluaran' => $request->tipe_pengeluaran,
                'nominal' => $request->nominal,
                'user_id_updated' => Auth::user()->id,
            ]);

            $beban = $request->tipe_pengeluaran == 'Listrik' ? '501' :
                     ($request->tipe_pengeluaran == 'Sewa' ? '502' :
                     ($request->tipe_pengeluaran == 'Air' ? '503' :
                     ($request->tipe_pengeluaran == 'Wifi' ? '504' : '505')));

            Jurnal::where('jenis_transaksi', 'pengeluaran')->where('id_transaksi', $id)->where('posisi_dr_cr', 'c')->update([
                'nominal' => $request->nominal,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            Jurnal::where('jenis_transaksi', 'pengeluaran')->where('id_transaksi', $id)->where('posisi_dr_cr', 'd')->update([
                'nominal' => $request->nominal,
                'id_coa' => Coa::where('kode_akun', $beban)->first()->id,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('pengeluaran.index')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')->with('error', 'Data gagal diupdate');
        }
    }

    public function destroy(string $id)
    {
        try {
            Pengeluaran::destroy($id);
            Jurnal::where('jenis_transaksi', 'pengeluaran')->where('id_transaksi', $id)->delete();
            return redirect()->route('pengeluaran.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pengeluaran.index')->with('error', 'Data gagal dihapus');
        }
    }
}
