<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\Reservasi;
use App\Models\User;

class NotifikasiService
{
    public static function createReservasiNotification($reservasi_id, $user_id)
    {
        $reservasi = Reservasi::find($reservasi_id);

        // Notifikasi untuk user yang membuat reservasi
        Notifikasi::create([
            'judul' => 'Reservasi Berhasil Dibuat',
            'pesan' => "Reservasi dengan nomor {$reservasi->no_reservasi} berhasil dibuat. Menunggu persetujuan admin.",
            'jenis' => 'reservasi_created',
            'user_id' => $user_id,
            'reservasi_id' => $reservasi_id,
        ]);

        // Notifikasi untuk admin (ambil semua user dengan role admin)
        $adminUsers = User::where('role', 'admin')->get();

        foreach ($adminUsers as $admin) {
            Notifikasi::create([
                'judul' => 'Reservasi Baru',
                'pesan' => "Ada reservasi baru dengan nomor {$reservasi->no_reservasi} dari pelanggan. Silakan tinjau.",
                'jenis' => 'reservasi_created',
                'user_id' => $admin->id,
                'reservasi_id' => $reservasi_id,
            ]);
        }
    }

    public static function approveReservasiNotification($reservasi_id)
    {
        $reservasi = Reservasi::find($reservasi_id);

        // Kirim notifikasi ke user yang membuat reservasi
        $user_id = $reservasi->user_id_created;

        Notifikasi::create([
            'judul' => 'Reservasi Disetujui',
            'pesan' => "Reservasi dengan nomor {$reservasi->no_reservasi} telah disetujui, silahkan datang pada waktu yang telah ditentukan. Terima kasih!",
            'jenis' => 'reservasi_approved',
            'user_id' => $user_id,
            'reservasi_id' => $reservasi_id,
            
        ]);
    }

    public static function cancelReservasiNotification($reservasi_id)
    {
        $reservasi = Reservasi::find($reservasi_id);

        // Kirim notifikasi ke user yang membuat reservasi
        $user_id = $reservasi->user_id_created;

        Notifikasi::create([
            'judul' => 'Reservasi Dibatalkan',
            'pesan' => "Reservasi dengan nomor {$reservasi->no_reservasi} telah dibatalkan.",
            'jenis' => 'reservasi_cancelled',
            'user_id' => $user_id,
            'reservasi_id' => $reservasi_id,
        ]);
    }

    public static function getUnreadCount($user_id)
    {
        return Notifikasi::where('user_id', $user_id)
            ->where('is_read', false)
            ->count();
    }

    public static function getUserNotifications($user_id, $limit = 10)
    {
        return Notifikasi::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function markAsRead($notifikasi_id)
    {
        Notifikasi::where('id', $notifikasi_id)->update(['is_read' => true]);
    }

    public static function markAllAsRead($user_id)
    {
        Notifikasi::where('user_id', $user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
    public static function getAdminNotifications($limit = 10)
    {
        return Notifikasi::whereHas('user', function ($query) {
            $query->where('role', 'admin');
        })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getAdminUnreadCount()
    {
        return Notifikasi::whereHas('user', function ($query) {
            $query->where('role', 'admin');
        })
            ->where('is_read', false)
            ->count();
    }
}
