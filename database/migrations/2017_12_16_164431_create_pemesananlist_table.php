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
        $table->integer('QTTerima')->default(0);
        $table->integer('QTRetur')->default(0);
        $table->integer('Amount');
				$table->string('PesanCode');
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
