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
        Schema::dropIfExists('pembelian_header');
        Schema::create('pembelian_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_pembelian', 10)->unique();
            $table->date('tgl_pembelian');
            $table->text('keterangan');
            $table->enum('status', ['finished', 'unfinished'])->default('unfinished');
            $table->unsignedBigInteger('id_vendor');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_vendor')->references('id')->on('vendor');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelian_header', function (Blueprint $table) {
            $table->dropForeign(['id_vendor']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('pembelian_header');
    }
};
