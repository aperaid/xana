<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransaksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('transaksi', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('Purchase')->unique();
        $table->string('JS');
        $table->string('Barang');
        $table->integer('Quantity');
        $table->integer('QSisaKirInsert');
        $table->integer('QSisaKir');
        $table->integer('QSisaKem')->default(0);
        $table->integer('Amount');
        $table->string('Reference');
        $table->string('POCode');
        $table->string('ICode');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('transaksi');
    }
}
