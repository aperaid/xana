<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransaksiexchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('transaksiexchange', function (Blueprint $table){
        $table->increments('id');
        $table->string('BExchange');
        $table->integer('QExchange');
        $table->integer('PExchange');
				$table->string('Reference');
				$table->integer('Periode');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('transaksiexchange');
    }
}
