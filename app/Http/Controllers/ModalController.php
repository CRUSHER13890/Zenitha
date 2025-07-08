<?php
// modal
namespace App\Http\Controllers;

use Autocode;
use App\Models\Coa;
use App\Models\Modal;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModalController extends Controller
{
    public function index()
    {
        $modals = Modal::all();
        return view('modal.index', ['modals' => $modals, 'title' => 'Modal']);
    }

    public function create()
    {
        $kode_modal = Autocode::code('modal', 'kode_modal', 'MDL');
        return view('modal.create', ['kode_modal' => $kode_modal, 'title' => 'Tambah Modal']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_modal' => 'required',
            'tgl_modal' => 'required|date',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
        ], [
            'kode_modal.required' => 'Kode Modal harus diisi',
            'tgl_modal.required' => 'Tanggal Modal harus diisi',
            'keterangan.required' => 'Keterangan harus diisi',
            'nominal.required' => 'Nominal harus diisi',
        ]);

        try {
            $modal = Modal::create([
                'kode_modal' => $request->kode_modal,
                'tgl_modal' => $request->tgl_modal,
                'keterangan' => $request->keterangan,
                'nominal' => $request->nominal,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            $check_akun_modal = Coa::where('kode_akun', '301')->first();
            $check_akun_kas = Coa::where('kode_akun', '101')->first();

            if (!$check_akun_modal) {
                $coa_modal = Coa::create([
                    'kode_akun' => '301',
                    'nama_akun' => 'Modal',
                    'header_akun' => '3',
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            if (!$check_akun_kas) {
                $coa_kas = Coa::create([
                    'kode_akun' => '101',
                    'nama_akun' => 'Kas',
                    'header_akun' => '1',
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            $jurnal_debit = Jurnal::create([
                'no_jurnal' => Autocode::code('jurnal', 'no_jurnal', 'JU'),
                'tgl_jurnal' => $request->tgl_modal,
                'posisi_dr_cr' => 'd',
                'nominal' => $request->nominal,
                'jenis_transaksi' => 'modal',
                'id_transaksi' => $modal->id,
                'id_coa' => !$check_akun_kas ? $coa_kas->id : $check_akun_kas->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            Jurnal::create([
                'no_jurnal' => $jurnal_debit->no_jurnal,
                'tgl_jurnal' => $request->tgl_modal,
                'posisi_dr_cr' => 'c',
                'nominal' => $request->nominal,
                'jenis_transaksi' => 'modal',
                'id_transaksi' => $modal->id,
                'id_coa' => !$check_akun_modal ? $coa_modal->id : $check_akun_modal->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('modal.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('modal.index')->with('error', 'Data gagal disimpan');
        }
    }

    public function edit($id)
    {
        $modal = Modal::findOrFail($id);
        return view('modal.edit', ['modal' => $modal, 'title' => 'Edit Modal']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kode_modal' => 'required',
            'tgl_modal' => 'required|date',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
        ], [
            'kode_modal.required' => 'Kode Modal harus diisi',
            'tgl_modal.required' => 'Tanggal Modal harus diisi',
            'keterangan.required' => 'Keterangan harus diisi',
            'nominal.required' => 'Nominal harus diisi',
        ]);

        try {
            Modal::where('id', $id)->update([
                'kode_modal' => $request->kode_modal,
                'tgl_modal' => $request->tgl_modal,
                'keterangan' => $request->keterangan,
                'nominal' => $request->nominal,
                'user_id_updated' => Auth::user()->id,
            ]);

            Jurnal::where('jenis_transaksi', 'modal')->where('id_transaksi', $id)->where('posisi_dr_cr', 'c')->update([
                'tgl_jurnal' => $request->tgl_modal,
                'nominal' => $request->nominal,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            Jurnal::where('jenis_transaksi', 'modal')->where('id_transaksi', $id)->where('posisi_dr_cr', 'd')->update([
                'tgl_jurnal' => $request->tgl_modal,
                'nominal' => $request->nominal,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('modal.index')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('modal.index')->with('error', 'Data gagal diperbarui');
        }
    }

    public function destroy($id)
    {
        try {
            Modal::destroy($id);
            Jurnal::where('jenis_transaksi', 'modal')->where('id_transaksi', $id)->delete();
            return redirect()->route('modal.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('modal.index')->with('error', 'Data gagal dihapus');
        }
    }
}
