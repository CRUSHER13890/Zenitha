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
        Schema::dropIfExists('penjualan_detail');
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->id();
            $table->double('kuantitas');
            $table->double('harga_satuan');
            $table->unsignedBigInteger('id_penjualan_header');
            $table->integer('diskon')->default(0);
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_diskon')->nullable();
            $table->bigInteger('subtotal');

            $table->timestamps();

            $table->foreign('id_barang')->references('id')->on('barang');
            $table->foreign('id_diskon')->references('id')->on('diskon');
            $table->foreign('id_penjualan_header')->references('id')->on('penjualan_header');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
            $table->dropForeign(['id_penjualan_header']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('penjualan_detail');
    }
};
