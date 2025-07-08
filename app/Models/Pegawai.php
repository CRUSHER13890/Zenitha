<?php
// pegawai
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';
    protected $fillable = [
        'kode_pegawai',
        'nama_pegawai',
        'no_hp',
        'jenis_kelamin',
        'tgl_lahir',
        'alamat',
        'user_id_created',
        'user_id_updated',
        'updated_at',
    ];
}
