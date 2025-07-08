<?php
// data coa
namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coa = Coa::all();
        return view('coa.index', ['coa' => $coa, 'title' => 'Chart of Account']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coa = Coa::all();
        return view('coa.create', ['title' => 'Tambah Chart of Account', 'coa' => $coa]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_akun' => 'required',
            'nama_akun' => 'required',
            'header_akun' => 'required',
            'saldo_awal' => 'nullable|numeric|min:0',
        ], [
            'kode_akun.required' => 'Kode Akun harus diisi',
            'nama_akun.required' => 'Nama Akun harus diisi',
            'header_akun.required' => 'Header Akun harus diisi',
            'saldo_awal.numeric' => 'Saldo Awal harus berupa angka',
            'saldo_awal.min' => 'Saldo Awal tidak boleh kurang dari 0',
        ]);

        try {
            Coa::create([
                'kode_akun' => $request->kode_akun,
                'nama_akun' => $request->nama_akun,
                'header_akun' => $request->header_akun == 'tidak' ? null : $request->header_akun,
                'saldo_awal' => $request->saldo_awal ?? 0,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('coa.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('coa.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $coa = Coa::find($id);
        $list_coa = Coa::all();
        return view('coa.edit', ['coa' => $coa, 'title' => 'Edit Chart of Account', 'list_coa' => $list_coa]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_akun' => 'required',
            'nama_akun' => 'required',
            'header_akun' => 'required',
            'saldo_awal' => 'nullable|numeric|min:0',
        ], [
            'kode_akun.required' => 'Kode Akun harus diisi',
            'nama_akun.required' => 'Nama Akun harus diisi',
            'header_akun.required' => 'Header Akun harus diisi',
            'saldo_awal.numeric' => 'Saldo Awal harus berupa angka',
            'saldo_awal.min' => 'Saldo Awal tidak boleh kurang dari 0',
        ]);

        try {
            Coa::where('id', $id)->update([
                'kode_akun' => $request->kode_akun,
                'nama_akun' => $request->nama_akun,
                'header_akun' => $request->header_akun == 'tidak' ? null : $request->header_akun,
                'saldo_awal' => $request->saldo_awal ?? 0,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('coa.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('coa.index')->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Coa::destroy($id);
            return redirect()->route('coa.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('coa.index')->with('error', 'Data gagal dihapus');
        }
    }
}
