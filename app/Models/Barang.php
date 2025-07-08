<?php
// barang
namespace App\Models;

use App\Models\Diskon;
use App\Models\Vendor;
use App\Models\BarangVendor;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'gambar_barang',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];

    public $timestamps = true;

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'barang_vendor', 'barang_id', 'vendor_id')
                    ->withPivot('harga_satuan', 'harga_jual', 'persentase_keuntungan', 'is_active', 'id')
                    ->withTimestamps();
    }

    public function barangVendors()
    {
        return $this->hasMany(BarangVendor::class, 'barang_id');
    }

    public function activeVendors()
    {
        return $this->belongsToMany(Vendor::class, 'barang_vendor', 'barang_id', 'vendor_id')
                    ->wherePivot('is_active', true)
                    ->withPivot('harga_satuan', 'harga_jual', 'persentase_keuntungan', 'is_active', 'id')
                    ->withTimestamps();
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'user_id_created');
    }

    public function user_updated()
    {
        return $this->belongsTo(User::class, 'user_id_updated');
    }

    public function diskon()
    {
        return $this->hasMany(Diskon::class, 'id_barang');
    }
}