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
        Schema::dropIfExists('vendor');
        Schema::create('vendor', function (Blueprint $table) {
            $table->id();
            $table->string('kode_vendor')->unique();
            $table->string('nama_vendor');
            $table->text('alamat_vendor');
            $table->string('no_hp');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor', function (Blueprint $table) {
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });

        Schema::dropIfExists('vendor');
    }
};
