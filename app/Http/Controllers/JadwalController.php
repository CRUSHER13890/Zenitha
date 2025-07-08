<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Layanan;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwal = DB::table('jadwal as a')
            ->leftJoin('layanan as b', 'a.id_layanan', '=', 'b.id')
            ->select('a.*', 'b.nama_layanan')
            ->get();

        return view('jadwal.index', ['jadwal' => $jadwal, 'title' => 'Jadwal']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kode_jadwal = Autocode::code('jadwal', 'kode_jadwal', 'JD');
        $layanan = Layanan::all();

        return view('jadwal.create', ['kode_jadwal' => $kode_jadwal, 'layanan' => $layanan, 'title' => 'Tambah Jadwal']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_jadwal' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'ruangan' => 'required',
            'id_layanan' => 'required',
        ], [
            'kode_jadwal.required' => 'Kode Jadwal harus diisi',
            'waktu_mulai.required' => 'Waktu Mulai harus diisi',
            'waktu_selesai.required' => 'Waktu Selesai harus diisi',
            'ruangan.required' => 'Ruangan harus diisi',
            'id_layanan.required' => 'Layanan harus diisi',
        ]);

        try {
            Jadwal::create([
                'kode_jadwal' => $request->kode_jadwal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'ruangan' => $request->ruangan,
                'id_layanan' => $request->id_layanan,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('jadwal.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('jadwal.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jadwal = Jadwal::find($id);
        $layanan = Layanan::all();

        return view('jadwal.edit', ['jadwal' => $jadwal, 'layanan' => $layanan, 'title' => 'Edit Jadwal']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kode_jadwal' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'ruangan' => 'required',
            'id_layanan' => 'required',
        ], [
            'kode_jadwal.required' => 'Kode Jadwal harus diisi',
            'waktu_mulai.required' => 'Waktu Mulai harus diisi',
            'waktu_selesai.required' => 'Waktu Selesai harus diisi',
            'ruangan.required' => 'Ruangan harus diisi',
            'id_layanan.required' => 'Layanan harus diisi',
        ]);

        try {
            Jadwal::where('id', $id)->update([
                'kode_jadwal' => $request->kode_jadwal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'ruangan' => $request->ruangan,
                'id_layanan' => $request->id_layanan,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->route('jadwal.index')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->route('jadwal.index')->with('error', 'Data gagal diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Jadwal::destroy($id);
            return redirect()->route('jadwal.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('jadwal.index')->with('error', 'Data gagal dihapus');
        }
    }
}
