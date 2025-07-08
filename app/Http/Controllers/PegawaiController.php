<?php
// masdat pegawai
namespace App\Http\Controllers;

use App\Models\Pegawai;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawai = Pegawai::all();
        return view('pegawai.index', ['pegawai' => $pegawai, 'title' => 'Pegawai']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_pegawai = Autocode::code('pegawai', 'kode_pegawai', 'PG');
        return view('pegawai.create', ['kode_pegawai' => $kode_pegawai, 'title' => 'Tambah Pegawai']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pegawai' => 'required',
            'nama_pegawai' => 'required',
            'no_hp' => 'required',
            'jenis_kelamin' => 'required',
            'tgl_lahir' => 'required',
            'alamat' => 'required',
        ], [
            'kode_pegawai.required' => 'Kode Pegawai harus diisi',
            'nama_pegawai.required' => 'Nama Pegawai harus diisi',
            'no_hp.required' => 'Nomor Handphone harus diisi',
            'jenis_kelamin.required' => 'Jenis Kelamin harus diisi',
            'tgl_lahir.required' => 'Tanggal Lahir harus diisi',
            'alamat.required' => 'Alamat Pegawai harus diisi',
        ]);

        try {
            Pegawai::create([
                'kode_pegawai' => $request->kode_pegawai,
                'nama_pegawai' => $request->nama_pegawai,
                'no_hp' => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'alamat' => $request->alamat,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('pegawai.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('pegawai.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pegawai = Pegawai::find($id);
        return view('pegawai.edit', ['pegawai' => $pegawai, 'title' => 'Edit Pegawai']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_pegawai' => 'required',
            'nama_pegawai' => 'required',
            'no_hp' => 'required',
            'jenis_kelamin' => 'required',
            'tgl_lahir' => 'required',
            'alamat' => 'required',
        ], [
            'kode_pegawai.required' => 'Kode Pegawai harus diisi',
            'nama_pegawai.required' => 'Nama Pegawai harus diisi',
            'no_hp.required' => 'Nomor Handphone harus diisi',
            'jenis_kelamin.required' => 'Jenis Kelamin harus diisi',
            'tgl_lahir.required' => 'Tanggal Lahir harus diisi',
            'alamat.required' => 'Alamat Pegawai harus diisi',
        ]);

        try {
            Pegawai::where('id', $id)->update([
                'kode_pegawai' => $request->kode_pegawai,
                'nama_pegawai' => $request->nama_pegawai,
                'no_hp' => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'alamat' => $request->alamat,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('pegawai.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('pegawai.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Pegawai::destroy($id);
            return redirect()->route('pegawai.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pegawai.index')->with('error', 'Data gagal dihapus');
        }
    }
}
