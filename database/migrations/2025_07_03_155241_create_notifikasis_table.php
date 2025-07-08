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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('pesan');
            $table->enum('jenis', ['reservasi_created', 'reservasi_approved', 'reservasi_cancelled']);
            $table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reservasi_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reservasi_id')->references('id')->on('reservasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::table('notifikasi', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropForeign(['reservasi_id']);
    }
    );
        Schema::dropIfExists('notifikasi');
    }
};