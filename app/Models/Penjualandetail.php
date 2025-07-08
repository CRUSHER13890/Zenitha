<?php

namespace App\Models;

use App\Models\Diskon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualandetail extends Model
{
    use HasFactory;

    protected $table = 'penjualan_detail';
    protected $fillable = [
        'kuantitas',
        'harga_satuan',
        'diskon',
        'subtotal',
        'id_barang',
        'id_diskon',
        'id_penjualan_header',
        'user_id_created',
        'user_id_updated'
    ];


    public function diskon()
    {
        return $this->belongsTo(Diskon::class, 'id_diskon');
    }
}
