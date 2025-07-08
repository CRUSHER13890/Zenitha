<?php
// LAYANAN C
namespace App\Http\Controllers;

use App\Models\Layanan;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $layanan = Layanan::all();

        return view('layanan.index', ['layanan' => $layanan, 'title' => 'Layanan']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_layanan = Autocode::code('Layanan', 'kode_layanan', 'LN');
        return view('layanan.create', ['kode_layanan' => $kode_layanan, 'title' => 'Tambah Layanan']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_layanan' => 'required',
            'nama_layanan' => 'required',
            'harga_layanan' => 'required',
            'deskripsi' => 'required',
        ], [
            'kode_layanan.required' => 'Kode Layanan harus diisi',
            'nama_layanan.required' => 'Nama Layanan harus diisi',
            'harga_layanan.required' => 'Harga Layanan harus diisi',
            'deskripsi.required' => 'Deskripsi harus diisi',
        ]);

        try {
            Layanan::create([
                'kode_layanan' => $request->kode_layanan,
                'nama_layanan' => $request->nama_layanan,
                'harga_layanan' => $request->harga_layanan,
                'deskripsi' => $request->deskripsi,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('layanan.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('layanan.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $layanan = Layanan::find($id);
        return view('layanan.edit', ['layanan' => $layanan, 'title' => 'Edit Layanan']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_layanan' => 'required',
            'nama_layanan' => 'required',
            'harga_layanan' => 'required',
            'deskripsi' => 'required',
        ], [
            'kode_layanan.required' => 'Kode Layanan harus diisi',
            'nama_layanan.required' => 'Nama Layanan harus diisi',
            'harga_layanan.required' => 'Harga Layanan harus diisi',
            'deskripsi.required' => 'Deskripsi harus diisi',
        ]);

        try {
            Layanan::where('id', $id)->update([
                'kode_layanan' => $request->kode_layanan,
                'nama_layanan' => $request->nama_layanan,
                'harga_layanan' => $request->harga_layanan,
                'deskripsi' => $request->deskripsi,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('layanan.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('layanan.index')->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Layanan::destroy($id);
            return redirect()->route('layanan.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('layanan.index')->with('error', 'Data gagal dihapus');
        }
    }
}
