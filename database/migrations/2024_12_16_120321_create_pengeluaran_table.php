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
        Schema::dropIfExists('pengeluaran');
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengeluaran', 10)->unique();
            $table->date('tgl_pengeluaran');
            $table->double('nominal');
            $table->enum('tipe_pengeluaran', ['Listrik', 'Sewa', 'Air', 'Wifi', 'Lainnya']);
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
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('pengeluaran');
    }
};
