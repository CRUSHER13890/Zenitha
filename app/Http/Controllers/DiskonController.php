<?php
// DiskonController.php
namespace App\Http\Controllers;

use Autocode;
use App\Models\Barang;
use App\Models\Diskon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Layanan; // Add this line to import Layanan model



class DiskonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $diskon = Diskon::with('barang', 'user', 'userUpdated')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('diskon.index', [
            'diskon' => $diskon,
            'title' => 'Diskon'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_diskon = Autocode::code('diskon', 'kode_diskon', 'DSK');
        $barang = Barang::all();
        $layanan = Layanan::all(); // Add this line to get all services
    
        return view('diskon.create', [
            'kode_diskon' => $kode_diskon,
            'barang' => $barang,
            'layanan' => $layanan, // Pass layanan to the view
            'title' => 'Tambah Diskon'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_diskon' => 'required',
            'nama_diskon' => 'required',
            'tipe_diskon' => 'required|in:barang,layanan',
            'persentase_diskon' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'kode_diskon.required' => 'Kode Diskon harus diisi',
            'nama_diskon.required' => 'Nama Diskon harus diisi',
            'tipe_diskon.required' => 'Tipe Diskon harus dipilih',
            'persentase_diskon.required' => 'Persentase Diskon harus diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai',
        ]);

        // Additional validation based on discount type
        if ($request->tipe_diskon === 'barang') {
            $request->validate([
                'min_transaksi' => 'required|numeric|min:0',
                'max_diskon' => 'required|numeric|min:0',
                'id_barang' => 'required|exists:barang,id',
            ], [
                'min_transaksi.required' => 'Minimal Transaksi harus diisi',
                'max_diskon.required' => 'Maksimal Diskon harus diisi',
                'id_barang.required' => 'Barang harus dipilih',
            ]);
        } else { // layanan
            $request->validate([
                'id_layanan' => 'required|exists:layanan,id',
            ], [
                'id_layanan.required' => 'Layanan harus dipilih',
            ]);
        }

        try {
            $diskonData = [
                'kode_diskon' => $request->kode_diskon,
                'nama_diskon' => $request->nama_diskon,
                'persentase_diskon' => $request->persentase_diskon,
                'tanggal_mulai' => $request->use_date_range ? $request->tanggal_mulai : null,
                'tanggal_selesai' => $request->use_date_range ? $request->tanggal_selesai : null,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ];

            // Add type-specific fields
            if ($request->tipe_diskon === 'barang') {
                $diskonData['min_transaksi'] = $request->min_transaksi;
                $diskonData['max_diskon'] = $request->max_diskon;
                $diskonData['id_barang'] = $request->id_barang;
                $diskonData['id_layanan'] = null;
            } else { // layanan
                $diskonData['min_transaksi'] = 0; // Default value for layanan
                $diskonData['max_diskon'] = 0; // Default value for layanan
                $diskonData['id_barang'] = null;
                $diskonData['id_layanan'] = $request->id_layanan;
            }

            Diskon::create($diskonData);
            return redirect()->route('diskon.index')->with('success', 'Diskon berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('diskon.index')->with('error', 'Gagal menambahkan diskon: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $diskon = Diskon::find($id);
        $barang = Barang::all();
        $layanan = Layanan::all(); // Add this line to get all services

        // Determine the discount type
        $tipe_diskon = $diskon->id_layanan ? 'layanan' : 'barang';
        
        // Check if date range is used
        $use_date_range = ($diskon->tanggal_mulai !== null || $diskon->tanggal_selesai !== null);

        return view('diskon.edit', [
            'diskon' => $diskon,
            'barang' => $barang,
            'layanan' => $layanan, // Pass layanan to the view
            'tipe_diskon' => $tipe_diskon, // Pass the discount type
            'use_date_range' => $use_date_range, // Pass whether date range is used
            'title' => 'Edit Diskon'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate common fields
        $rules = [
            'kode_diskon' => 'required',
            'nama_diskon' => 'required',
            'persentase_diskon' => 'required|numeric|min:0|max:100',
            'tipe_diskon' => 'required|in:barang,layanan', // Add this to determine discount type
        ];
        
        // Add date range validation if it's used
        if ($request->has('use_date_range')) {
            $rules['tanggal_mulai'] = 'required|date';
            $rules['tanggal_selesai'] = 'required|date|after_or_equal:tanggal_mulai';
        }
        
        $messages = [
            'kode_diskon.required' => 'Kode Diskon harus diisi',
            'nama_diskon.required' => 'Nama Diskon harus diisi',
            'persentase_diskon.required' => 'Persentase Diskon harus diisi',
            'tipe_diskon.required' => 'Tipe Diskon harus dipilih',
            'tanggal_mulai.required' => 'Tanggal Mulai harus diisi jika periode diskon diaktifkan',
            'tanggal_selesai.required' => 'Tanggal Selesai harus diisi jika periode diskon diaktifkan',
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai',
        ];
        
        // Add conditional validation based on discount type
        if ($request->tipe_diskon == 'barang') {
            $rules['min_transaksi'] = 'required|numeric|min:0';
            $rules['max_diskon'] = 'required|numeric|min:0';
            $rules['id_barang'] = 'required|exists:barang,id';
            
            $messages['min_transaksi.required'] = 'Minimal Transaksi harus diisi';
            $messages['max_diskon.required'] = 'Maksimal Diskon harus diisi';
            $messages['id_barang.required'] = 'Barang harus dipilih';
        } else {
            $rules['id_layanan'] = 'required|exists:layanan,id';
            $messages['id_layanan.required'] = 'Layanan harus dipilih';
        }
        
        $validated = $request->validate($rules, $messages);

        try {
            $diskon = Diskon::find($id);
            $diskonData = [
                'kode_diskon' => $request->kode_diskon,
                'nama_diskon' => $request->nama_diskon,
                'persentase_diskon' => $request->persentase_diskon,
                'tanggal_mulai' => $request->has('use_date_range') ? $request->tanggal_mulai : null,
                'tanggal_selesai' => $request->has('use_date_range') ? $request->tanggal_selesai : null,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ];
            
            // Add type-specific fields
            if ($request->tipe_diskon == 'barang') {
                $diskonData['min_transaksi'] = $request->min_transaksi;
                $diskonData['max_diskon'] = $request->max_diskon;
                $diskonData['id_barang'] = $request->id_barang;
                $diskonData['id_layanan'] = null;
            } else {
                $diskonData['min_transaksi'] = 0; // Default value for layanan
                $diskonData['max_diskon'] = 0; // Default value for layanan
                $diskonData['id_barang'] = null;
                $diskonData['id_layanan'] = $request->id_layanan;
            }
            
            $diskon->update($diskonData);
            return redirect()->route('diskon.index')->with('success', 'Diskon berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('diskon.index')->with('error', 'Gagal memperbarui diskon: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $diskon = Diskon::find($id);
        try {
            $diskon->delete();
            return redirect()->route('diskon.index')->with('success', 'Diskon berhasil dihapus');
        } catch (\Exception $e) {
            // Check if it's a foreign key constraint violation
            if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                return redirect()->route('diskon.index')->with('error', 'Gagal menghapus diskon. Diskon ini sedang digunakan dalam transaksi penjualan.');
            }

            // For other errors, return a generic message
            return redirect()->route('diskon.index')->with('error', 'Gagal menghapus diskon');
        }
    }
}
