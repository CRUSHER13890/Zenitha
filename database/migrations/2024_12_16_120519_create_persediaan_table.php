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
        Schema::dropIfExists('persediaan');
        Schema::create('persediaan', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tgl_persediaan');
            $table->text('keterangan');
            $table->double('kuantitas');
            $table->double('harga_beli_satuan');
            $table->double('harga_jual_satuan');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_pembelian_detail');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_barang')->references('id')->on('barang');
            $table->foreign('id_pembelian_detail')->references('id')->on('pembelian_detail');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persediaan', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
            $table->dropForeign(['id_pembelian_detail']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('persediaan');
    }
};
