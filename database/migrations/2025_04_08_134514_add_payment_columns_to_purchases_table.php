<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('diskon_poin')->default(0);
            $table->integer('total_bayar')->default(0);
            $table->integer('kembalian')->default(0);
        });
    }
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['diskon_poin', 'total_bayar', 'kembalian']);
        });
    }
};
