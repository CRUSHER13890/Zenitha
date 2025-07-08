<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('pengambilan');
        Schema::create('pengambilan', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tgl_pengambilan');
            $table->text('keterangan');
            $table->double('kuantitas');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_persediaan');
            $table->unsignedBigInteger('id_penjualan_detail');
            $table->double('harga_satuan')->nullable();
            $table->text('keterangan_fifo')->nullable();
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_barang')->references('id')->on('barang');
            $table->foreign('id_persediaan')->references('id')->on('persediaan');
            $table->foreign('id_penjualan_detail')->references('id')->on('penjualan_detail');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengambilan', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
            $table->dropForeign(['id_persediaan']);
            $table->dropForeign(['id_penjualan_detail']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('pengambilan');
    }
};
