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
        Schema::dropIfExists('reservasi');
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->string('no_reservasi', 10)->unique();
            $table->unsignedBigInteger('id_layanan');
            $table->string('ruangan')->nullable();
            $table->date('tgl_reservasi');
            $table->enum('status', ['Disetujui', 'Batal', 'pending','Selesai'])->default('pending');
            $table->unsignedBigInteger('id_pelanggan');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable();
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id')->on('pelanggan');
            $table->foreign('id_layanan')->references('id')->on('layanan');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('reservasi');
    }
};
