<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengambilan extends Model
{
    use HasFactory;

    protected $table = 'pengambilan';
    protected $fillable = [
        'tgl_pengambilan',
        'keterangan',
        'kuantitas',
        'id_barang',
        'id_persediaan',
        'id_penjualan_detail',
        'harga_satuan',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
