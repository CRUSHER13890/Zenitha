<?php
// pelanggan c
namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggan = Pelanggan::all();
        return view('pelanggan.index', ['pelanggan' => $pelanggan, 'title' => 'Pelanggan']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_pelanggan = Autocode::code('pelanggan', 'kode_pelanggan', 'PL');
        return view('pelanggan.create', ['kode_pelanggan' => $kode_pelanggan, 'title' => 'Tambah Pelanggan']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pelanggan' => 'required',
            'nama_pelanggan' => 'required',
            'no_hp' => 'required',
            'tgl_lahir' => 'required',
        ], [
            'kode_pelanggan.required' => 'Kode Pelanggan harus diisi',
            'nama_pelanggan.required' => 'Nama Pelanggan harus diisi',
            'no_hp.required' => 'Nomor Handphone harus diisi',
            'tgl_lahir.required' => 'Tanggal Lahir harus diisi',
        ]);

        try {
            Pelanggan::create([
                'kode_pelanggan' => $request->kode_pelanggan,
                'nama_pelanggan' => $request->nama_pelanggan,
                'no_hp' => $request->no_hp,
                'tgl_lahir' => $request->tgl_lahir,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('pelanggan.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('pelanggan.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pelanggan = Pelanggan::find($id);
        return view('pelanggan.edit', ['pelanggan' => $pelanggan, 'title' => 'Edit Pelanggan']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_pelanggan' => 'required',
            'nama_pelanggan' => 'required',
            'no_hp' => 'required',
            'tgl_lahir' => 'required',
        ], [
            'kode_pelanggan.required' => 'Kode Pelanggan harus diisi',
            'nama_pelanggan.required' => 'Nama Pelanggan harus diisi',
            'no_hp.required' => 'Nomor Handphone harus diisi',
            'tgl_lahir.required' => 'Tanggal Lahir harus diisi',
        ]);

        try {
            Pelanggan::where('id', $id)->update([
                'kode_pelanggan' => $request->kode_pelanggan,
                'nama_pelanggan' => $request->nama_pelanggan,
                'no_hp' => $request->no_hp,
                'tgl_lahir' => $request->tgl_lahir,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('pelanggan.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('pelanggan.index')->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Pelanggan::destroy($id);
            return redirect()->route('pelanggan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pelanggan.index')->with('error', 'Data gagal dihapus');
        }
    }
}
