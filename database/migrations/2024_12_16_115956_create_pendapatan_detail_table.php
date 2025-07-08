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
        Schema::dropIfExists('pendapatan_detail');
        Schema::create('pendapatan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_reservasi');
            $table->double('harga');
            $table->double('diskon')->nullable();
            $table->string('keterangan_diskon')->nullable();
            $table->double('subtotal');
            $table->unsignedBigInteger('id_layanan');
            $table->unsignedBigInteger('id_pendapatan_header');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_pendapatan_header')->references('id')->on('pendapatan_header');
            $table->foreign('id_layanan')->references('id')->on('layanan');
            $table->foreign('id_reservasi')->references('id')->on('reservasi');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendapatan_detail', function (Blueprint $table) {
            $table->dropForeign(['id_pendapatan_header']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('pendapatan_detail');
    }
};
