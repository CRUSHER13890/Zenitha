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
        Schema::dropIfExists('jurnal');
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->string('no_jurnal', 10);
            $table->timestamp('tgl_jurnal');
            $table->enum('posisi_dr_cr', ['d', 'c']);
            $table->double('nominal');
            $table->string('jenis_transaksi');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_coa');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_coa')->references('id')->on('coa');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal', function (Blueprint $table) {
            $table->dropForeign(['id_coa']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('jurnal');
    }
};
