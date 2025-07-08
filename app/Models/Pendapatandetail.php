<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendapatandetail extends Model
{
    use HasFactory;

    protected $table = 'pendapatan_detail';
    protected $fillable = [
        'id_reservasi',
        'harga',
        'diskon',
        'keterangan_diskon',
        'id_layanan',
        'subtotal',
        'id_pendapatan_header',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
