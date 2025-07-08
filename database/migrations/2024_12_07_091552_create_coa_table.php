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
        Schema::dropIfExists('coa');
        Schema::create('coa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun')->unique();
            $table->string('nama_akun');
            $table->unsignedBigInteger('header_akun')->nullable();
            $table->double('saldo_awal')->nullable()->default(0);
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('header_akun')->references('id')->on('coa');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coa', function (Blueprint $table) {
            $table->dropForeign(['header_akun']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });

        Schema::dropIfExists('coa');
    }
};
