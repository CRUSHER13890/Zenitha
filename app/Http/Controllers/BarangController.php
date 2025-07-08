<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Vendor;
use App\Models\BarangVendor;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang = Barang::with(['barangVendors.vendor'])->get();
        
        return view('barang.index', ['barang' => $barang, 'title' => 'Barang']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_barang = Autocode::code('barang', 'kode_barang', 'BG');
        $vendor = Vendor::all();

        return view('barang.create', ['kode_barang' => $kode_barang, 'vendor' => $vendor, 'title' => 'Tambah Barang']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'gambar_barang' => 'file|image|mimes:jpeg,png,jpg|max:2048',
            'vendors' => 'required|array|min:1',
            'vendors.*.id' => 'required|exists:vendor,id',
            'vendors.*.harga_satuan' => 'required|numeric|min:0',
            'vendors.*.persentase_keuntungan' => 'required|numeric|min:0|max:100',
        ], [
            'kode_barang.required' => 'Kode Barang harus diisi',
            'nama_barang.required' => 'Nama Barang harus diisi',
            'vendors.required' => 'Minimal satu vendor harus dipilih',
            'vendors.*.id.required' => 'Vendor harus dipilih',
            'vendors.*.harga_satuan.required' => 'Harga Satuan harus diisi',
            'vendors.*.persentase_keuntungan.required' => 'Persentase Keuntungan harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $file_name = 'dummy-image.png';
            
            if ($request->hasFile('gambar_barang')) {
                $file = $request->file('gambar_barang');
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $image = Image::read($file);
                $image->resize(289, 289, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('assets/images/barang/' . $file_name));
            }

            $barang = Barang::create([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'gambar_barang' => $file_name,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            // Create barang-vendor relationships
            foreach ($request->vendors as $vendor_data) {
                $harga_satuan = $vendor_data['harga_satuan'];
                $persentase = $vendor_data['persentase_keuntungan'];
                $harga_jual = $harga_satuan + ($harga_satuan * $persentase / 100);

                BarangVendor::create([
                    'barang_id' => $barang->id,
                    'vendor_id' => $vendor_data['id'],
                    'harga_satuan' => $harga_satuan,
                    'harga_jual' => $harga_jual,
                    'persentase_keuntungan' => $persentase,
                    'is_active' => true,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('barang.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barang = Barang::with(['barangVendors.vendor'])->find($id);
        $vendor = Vendor::all();

        return view('barang.edit', ['barang' => $barang, 'vendor' => $vendor, 'title' => 'Edit Barang']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'gambar_barang' => 'file|image|mimes:jpeg,png,jpg|max:2048',
            'vendors' => 'required|array|min:1',
            'vendors.*.id' => 'required|exists:vendor,id',
            'vendors.*.harga_satuan' => 'required|numeric|min:0',
            'vendors.*.persentase_keuntungan' => 'required|numeric|min:0|max:100',
        ], [
            'kode_barang.required' => 'Kode Barang harus diisi',
            'nama_barang.required' => 'Nama Barang harus diisi',
            'vendors.required' => 'Minimal satu vendor harus dipilih',
            'vendors.*.id.required' => 'Vendor harus dipilih',
            'vendors.*.harga_satuan.required' => 'Harga Satuan harus diisi',
            'vendors.*.persentase_keuntungan.required' => 'Persentase Keuntungan harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::find($id);
            $file_name = $barang->gambar_barang;

            if ($request->hasFile('gambar_barang')) {
                if (file_exists(public_path('assets/images/barang/' . $barang->gambar_barang)) && $barang->gambar_barang != 'dummy-image.png') {
                    unlink(public_path('assets/images/barang/' . $barang->gambar_barang));
                }

                $file = $request->file('gambar_barang');
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $image = Image::read($file);
                $image->resize(289, 289, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('assets/images/barang/' . $file_name));
            }

            $barang->update([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'gambar_barang' => $file_name,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            // Delete existing barang-vendor relationships
            BarangVendor::where('barang_id', $id)->delete();

            // Create new barang-vendor relationships
            foreach ($request->vendors as $vendor_data) {
                $harga_satuan = $vendor_data['harga_satuan'];
                $persentase = $vendor_data['persentase_keuntungan'];
                $harga_jual = $harga_satuan + ($harga_satuan * $persentase / 100);

                BarangVendor::create([
                    'barang_id' => $barang->id,
                    'vendor_id' => $vendor_data['id'],
                    'harga_satuan' => $harga_satuan,
                    'harga_jual' => $harga_jual,
                    'persentase_keuntungan' => $persentase,
                    'is_active' => true,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }

            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('barang.index')->with('error', 'Data gagal diperbarui');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::find($id);
        DB::beginTransaction();
        try {
            // Delete barang-vendor relationships
            BarangVendor::where('barang_id', $id)->delete();
            
            // Delete barang
            $barang->delete();

            if (file_exists(public_path('assets/images/barang/' . $barang->gambar_barang)) && $barang->gambar_barang != 'dummy-image.png') {
                unlink(public_path('assets/images/barang/' . $barang->gambar_barang));
            }

            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('barang.index')->with('error', 'Data gagal dihapus');
        }
    }
}