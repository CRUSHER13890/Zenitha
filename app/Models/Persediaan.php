<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persediaan extends Model
{
    protected $table = 'persediaan';
    protected $fillable = [
        'tgl_persediaan',
        'keterangan',
        'kuantitas',
        'harga_beli_satuan',
        'harga_jual_satuan',
        'id_barang',
        'id_pembelian_detail',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
