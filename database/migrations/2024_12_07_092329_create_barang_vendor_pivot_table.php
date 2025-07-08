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
        // Create pivot table for barang-vendor relationship
        Schema::create('barang_vendor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('vendor_id');
            $table->double('harga_satuan');
            $table->double('harga_jual');
            $table->double('persentase_keuntungan');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendor')->onDelete('cascade');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
            
            // Unique constraint to prevent duplicate barang-vendor combinations
            $table->unique(['barang_id', 'vendor_id']);
        });

        // Modify existing barang table - remove vendor-specific fields
        // Schema::table('barang', function (Blueprint $table) {
        //     $table->dropForeign(['id_vendor']);
        //     $table->dropColumn(['id_vendor', 'harga_satuan', 'harga_jual']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_vendor');
       
    }
};