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
        Schema::create('modal', function (Blueprint $table) {
            $table->id();
            $table->string('kode_modal')->unique();
            $table->date('tgl_modal');
            $table->string('keterangan');
            $table->double('nominal');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modal', function (Blueprint $table) {
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });

        Schema::dropIfExists('modal');
        
    }
};
