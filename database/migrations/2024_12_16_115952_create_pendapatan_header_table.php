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
        Schema::dropIfExists('pendapatan_header');
        Schema::create('pendapatan_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendapatan', 10)->unique();
            $table->date('tgl_pendapatan');
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_pelanggan')->references('id')->on('pelanggan');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendapatan_header', function (Blueprint $table) {
            $table->dropForeign(['id_pelanggan']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('pendapatan_header');
    }
};
