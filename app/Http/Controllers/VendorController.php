<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendor = Vendor::all();
        return view('vendor.index', ['vendor' => $vendor, 'title' => 'Vendor']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_vendor = Autocode::code('vendor', 'kode_vendor', 'PR');
        return view('vendor.create', ['kode_vendor' => $kode_vendor, 'title' => 'Tambah Vendor']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_vendor' => 'required',
            'nama_vendor' => 'required',
            'alamat_vendor' => 'required',
            'no_hp' => 'required',
        ], [
            'kode_vendor.required' => 'Kode Vendor harus diisi',
            'nama_vendor.required' => 'Nama Vendor harus diisi',
            'alamat_vendor.required' => 'Alamat Vendor harus diisi',
            'no_hp.required' => 'Nomor Handphone harus diisi',
        ]);

        try {
            Vendor::create([
                'kode_vendor' => $request->kode_vendor,
                'nama_vendor' => $request->nama_vendor,
                'alamat_vendor' => $request->alamat_vendor,
                'no_hp' => $request->no_hp,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('vendor.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('vendor.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vendor = Vendor::find($id);
        return view('vendor.edit', ['vendor' => $vendor, 'title' => 'Edit Vendor']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_vendor' => 'required',
            'nama_vendor' => 'required',
            'alamat_vendor' => 'required',
            'no_hp' => 'required',
        ], [
            'kode_vendor.required' => 'Kode Vendor harus diisi',
            'nama_vendor.required' => 'Nama Vendor harus diisi',
            'alamat_vendor.required' => 'Alamat Vendor harus diisi',
            'no_hp.required' => 'Nomor Handphone harus diisi',
        ]);

        try {
            Vendor::where('id', $id)->update([
                'kode_vendor' => $request->kode_vendor,
                'nama_vendor' => $request->nama_vendor,
                'alamat_vendor' => $request->alamat_vendor,
                'no_hp' => $request->no_hp,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('vendor.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('vendor.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Vendor::destroy($id);
            return redirect()->route('vendor.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('vendor.index')->with('error', 'Data gagal dihapus');
        }
    }
}
