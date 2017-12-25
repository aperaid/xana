<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePemesananlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('pemesananlist', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('Quantity');
        $table->integer('Amount');
				$table->string('PesanCode');
        $table->integer('QTerima')->nullable();
        $table->string('TerimaCode')->nullable();
        $table->integer('QRetur')->nullable();
        $table->string('ReturCode')->nullable();
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
      Schema::drop('pemesananlist');
    }
}
