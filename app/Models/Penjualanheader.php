<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualanheader extends Model
{
    use HasFactory;

    protected $table = 'penjualan_header';
    protected $fillable = [
        'no_penjualan',
        'tgl_penjualan',
        'keterangan',
        'status_pembayaran',
        'id_pelanggan',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
