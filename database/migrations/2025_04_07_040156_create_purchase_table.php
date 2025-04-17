<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            // Optional member_id (nullable)
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
            // Total harga seluruh produk
            $table->integer('total_price');
            // Tanggal pembelian
            $table->date('purchase_date');
            // User yang membuat pembelian
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('purchase');
    }
};
