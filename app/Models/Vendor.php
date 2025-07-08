<?php

namespace App\Models;

use App\Models\Barang;
use App\Models\BarangVendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';
    protected $fillable = [
        'kode_vendor',
        'nama_vendor',
        'alamat_vendor',
        'no_hp',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];

    public function barangs()
    {
        return $this->belongsToMany(Barang::class, 'barang_vendor', 'vendor_id', 'barang_id')
                    ->withPivot('harga_satuan', 'harga_jual', 'persentase_keuntungan', 'is_active', 'id')
                    ->withTimestamps();
    }

    public function barangVendors()
    {
        return $this->hasMany(BarangVendor::class, 'vendor_id');
    }

    public function activeBarangs()
    {
        return $this->belongsToMany(Barang::class, 'barang_vendor', 'vendor_id', 'barang_id')
                    ->wherePivot('is_active', true)
                    ->withPivot('harga_satuan', 'harga_jual', 'persentase_keuntungan', 'is_active', 'id')
                    ->withTimestamps();
    }
}