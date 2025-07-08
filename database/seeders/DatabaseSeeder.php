<?php

namespace Database\Seeders;

use App\Models\Coa;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123123123',
            'role' => 'admin',
        ]);

        Coa::insert([
            [
                'id' => 1,
                'kode_akun' => '1',
                'nama_akun' => 'Aset',
                'header_akun' => null,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'kode_akun' => '2',
                'nama_akun' => 'Kewajiban',
                'header_akun' => null,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'kode_akun' => '3',
                'nama_akun' => 'Modal',
                'header_akun' => null,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'kode_akun' => '4',
                'nama_akun' => 'Pendapatan',
                'header_akun' => null,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'kode_akun' => '5',
                'nama_akun' => 'Beban',
                'header_akun' => null,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'kode_akun' => '101',
                'nama_akun' => 'Kas',
                'header_akun' => 1,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'kode_akun' => '106',
                'nama_akun' => 'Persediaan Barang Dagang',
                'header_akun' => 1,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'kode_akun' => '301',
                'nama_akun' => 'Modal',
                'header_akun' => 3,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'kode_akun' => '401',
                'nama_akun' => 'Pendapatan Jasa',
                'header_akun' => 4,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'kode_akun' => '406',
                'nama_akun' => 'Penjualan',
                'header_akun' => 4,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'kode_akun' => '411',
                'nama_akun' => 'Diskon Penjualan',
                'header_akun' => 4,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'kode_akun' => '412',
                'nama_akun' => 'Diskon Pendapatan',
                'header_akun' => 4,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'kode_akun' => '511',
                'nama_akun' => 'Harga Pokok Penjualan',
                'header_akun' => 5,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'kode_akun' => '501',
                'nama_akun' => 'Beban Listrik',
                'header_akun' => 5,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'kode_akun' => '502',
                'nama_akun' => 'Beban Sewa',
                'header_akun' => 5,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 16,
                'kode_akun' => '503',
                'nama_akun' => 'Beban Air',
                'header_akun' => 5,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 17,
                'kode_akun' => '504',
                'nama_akun' => 'Beban Wifi',
                'header_akun' => 5,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 18,
                'kode_akun' => '505',
                'nama_akun' => 'Beban Lainnya',
                'header_akun' => 5,
                'user_id_created' => 1,
                'user_id_updated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
