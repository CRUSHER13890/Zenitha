<?php

namespace App\Http\Controllers;

use Autocode;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\NotifikasiService;

class ReservasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('reservasi as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->leftJoin('layanan as d', 'a.id_layanan', '=', 'd.id')
            ->select('a.id', 'a.no_reservasi', 'a.tgl_reservasi', 'b.nama_pelanggan', 'd.nama_layanan', 'a.waktu_mulai', 'a.waktu_selesai', 'a.status', 'a.ruangan', 'd.harga_layanan', 'a.user_id_created', 'a.user_id_updated', 'a.created_at', 'a.updated_at');

        // Filter berdasarkan tanggal jika ada
        if ($request->has('filter_date')) {
            $query->where('a.tgl_reservasi', $request->filter_date);
        }

        $reservasi = $query->get();

        return view('reservasi.index', [
            'reservasi' => $reservasi,
            'title' => 'Reservasi',
            'filter_date' => $request->filter_date ?? ''
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $no_reservasi = Autocode::code('reservasi', 'no_reservasi', 'RT');

        $pelanggan = Pelanggan::all();

        $layanan = Layanan::all();

        return view('reservasi.create', ['no_reservasi' => $no_reservasi, 'pelanggan' => $pelanggan, 'layanan' => $layanan, 'title' => 'Tambah Reservasi']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_reservasi' => 'required',
            'tgl_reservasi' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'id_pelanggan' => 'required',
            'id_layanan' => 'required',
            'ruangan' => 'required|in:Ruangan 1,Ruangan 2,Ruangan 3',
        ], [
            'no_reservasi.required' => 'Nomor Reservasi harus diisi',
            'tgl_reservasi.required' => 'Tanggal Reservasi harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
            'id_layanan.required' => 'Jadwal harus diisi',
            'waktu_mulai.required' => 'Waktu Mulai harus diisi',
            'waktu_selesai.required' => 'Waktu Selesai harus diisi',
            'waktu_selesai.after' => 'Waktu Selesai harus lebih dari Waktu Mulai',
            'ruangan.required' => 'Ruangan harus diisi',
            'ruangan.in' => 'Ruangan harus Ruangan 1, Ruangan 2 atau Ruangan 3',
        ]);

        // Check for overlapping schedules in the same room
        $existingReservation = Reservasi::where('ruangan', $request->ruangan)
            ->where('tgl_reservasi', $request->tgl_reservasi)
            ->where('status', 'Disetujui') // Only check approved reservations
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                    });
            })
            ->first();

        if ($existingReservation) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['scheduling' => 'Jadwal waktu tersebut sudah terisi untuk ruangan ini. Silakan pilih waktu atau ruangan lain.']);
        }

        try {
            Reservasi::create([
                'no_reservasi' => $request->no_reservasi,
                'tgl_reservasi' => $request->tgl_reservasi,
                'status' => 'Disetujui',
                'id_pelanggan' => $request->id_pelanggan,
                'id_layanan' => $request->id_layanan,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'ruangan' => $request->ruangan,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            return redirect()->route('reservasi.index')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('reservasi.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $reservasi = Reservasi::find($id);

        $pelanggan = Pelanggan::all();

        $layanan = Layanan::all();

        return view('reservasi.edit', ['reservasi' => $reservasi, 'pelanggan' => $pelanggan, 'layanan' => $layanan, 'title' => 'Edit Reservasi']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tgl_reservasi' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'id_pelanggan' => 'required',
            'id_layanan' => 'required',
            'ruangan' => 'required|in:Ruangan 1,Ruangan 2,Ruangan 3',
        ], [
            'tgl_reservasi.required' => 'Tanggal Reservasi harus diisi',
            'id_pelanggan.required' => 'Pelanggan harus diisi',
            'id_layanan.required' => 'Jadwal harus diisi',
            'waktu_mulai.required' => 'Waktu Mulai harus diisi',
            'waktu_selesai.required' => 'Waktu Selesai harus diisi',
            'waktu_selesai.after' => 'Waktu Selesai harus lebih dari Waktu Mulai',
            'ruangan.required' => 'Ruangan harus diisi',
            'ruangan.in' => 'Ruangan harus Ruangan 1, Ruangan 2 atau Ruangan 3',
        ]);

        // Check for overlapping schedules
        $existingReservation = Reservasi::where('ruangan', $request->ruangan)
            ->where('tgl_reservasi', $request->tgl_reservasi)
            ->where('id', '!=', $id)
            ->where('status', 'Disetujui')
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                    });
            })
            ->first();

        if ($existingReservation) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['scheduling' => 'Jadwal waktu tersebut sudah terisi untuk ruangan ini. Silakan pilih waktu atau ruangan lain.']);
        }

        try {
            $reservasi = Reservasi::find($id);
            $isNewlyApproved = !$reservasi->ruangan && !$reservasi->waktu_selesai && $request->status == 'Disetujui';

            Reservasi::where('id', $id)->update([
                'tgl_reservasi' => $request->tgl_reservasi,
                'id_pelanggan' => $request->id_pelanggan,
                'id_layanan' => $request->id_layanan,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'ruangan' => $request->ruangan,
                'status' => $request->status,
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            // Kirim notifikasi jika ini adalah approval baru
            if ($isNewlyApproved) {
                NotifikasiService::approveReservasiNotification($id);
            }

            return redirect()->route('reservasi.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('reservasi.index')->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Reservasi::destroy($id);

            return redirect()->route('reservasi.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('reservasi.index')->with('error', 'Data gagal dihapus');
        }
    }

    public function approve(string $id)
    {
        $reservasi = Reservasi::find($id);

        if (!$reservasi->ruangan || !$reservasi->waktu_selesai) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harap lengkapi data ruangan dan waktu selesai terlebih dahulu!'
                ]);
            }
            return redirect()->route('reservasi.edit', $id)
                ->with('warning', 'Harap lengkapi data ruangan dan waktu selesai terlebih dahulu!');
        }

        try {
            Reservasi::where('id', $id)->update([
                'status' => 'Disetujui',
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            NotifikasiService::approveReservasiNotification($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reservasi berhasil disetujui'
                ]);
            }
            return redirect()->route('reservasi.index')->with('success', 'Data berhasil Disetujui');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyetujui reservasi'
                ]);
            }
            return redirect()->route('reservasi.index')->with('error', 'Data gagal Disetujui');
        }
    }

    public function cancel(string $id)
    {
        try {
            Reservasi::where('id', $id)->update([
                'status' => 'Batal',
                'user_id_updated' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            NotifikasiService::cancelReservasiNotification($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reservasi berhasil dibatalkan'
                ]);
            }
            return redirect()->route('reservasi.index')->with('success', 'Data berhasil dicancel');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membatalkan reservasi'
                ]);
            }
            return redirect()->route('reservasi.index')->with('error', 'Data gagal dicancel');
        }
    }
}
