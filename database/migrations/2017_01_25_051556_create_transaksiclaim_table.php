<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransaksiclaimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('transaksiclaim', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('Claim')->unique();
        $table->string('Tgl');
        $table->integer('QClaim');
        $table->integer('Amount');
        $table->integer('Purchase');
        $table->integer('Periode');
        $table->integer('IsiSJKir');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('transaksiclaim');
    }
}
