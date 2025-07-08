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
        Schema::create('diskon', function (Blueprint $table) {
            $table->id();
            $table->string('kode_diskon')->unique();
            $table->string('nama_diskon');
            $table->bigInteger('min_transaksi')->nullable();
            $table->bigInteger('persentase_diskon');
            $table->bigInteger('max_diskon')->nullable();
            $table->unsignedBigInteger('id_barang')->nullable();
            $table->unsignedBigInteger('id_layanan')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_barang')->references('id')->on('barang')->onDelete('cascade');
            $table->foreign('id_layanan')->references('id')->on('layanan')->onDelete('cascade');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diskon', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
            $table->dropForeign(['id_layanan']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });

        Schema::dropIfExists('diskon');
    }
};
