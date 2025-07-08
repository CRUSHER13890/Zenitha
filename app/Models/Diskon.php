<?php

namespace App\Models;

use App\Models\Barang;
use App\Models\Layanan;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diskon extends Model
{
    use HasFactory;

    protected $table = 'diskon';

    protected $fillable = [
        'kode_diskon',
        'nama_diskon',
        'min_transaksi',
        'persentase_diskon',
        'max_diskon',
        'id_barang',
        'id_layanan',
        'tanggal_mulai',
        'tanggal_selesai',
        'user_id_created',
        'user_id_updated',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_created');
    }

    public function userUpdated()
    {
        return $this->belongsTo(User::class, 'user_id_updated');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'id_layanan');
    }

    public function isActive()
    {
        $today = now()->format('Y-m-d');

        // If no dates are set, the discount is always active
        if (is_null($this->tanggal_mulai) && is_null($this->tanggal_selesai)) {
            return true;
        }

        // If only start date is set, check if today is after or equal to start date
        if (!is_null($this->tanggal_mulai) && is_null($this->tanggal_selesai)) {
            return $today >= $this->tanggal_mulai;
        }

        // If only end date is set, check if today is before or equal to end date
        if (is_null($this->tanggal_mulai) && !is_null($this->tanggal_selesai)) {
            return $today <= $this->tanggal_selesai;
        }

        // If both dates are set, check if today is between start and end dates
        return $today >= $this->tanggal_mulai && $today <= $this->tanggal_selesai;
    }
}
