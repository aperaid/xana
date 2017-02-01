<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenawaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('penawaran', function (Blueprint $table) {
        $table->increments('id');
        $table->string('Penawaran')->unique();
        $table->string('Tgl');
        $table->string('Barang');
        $table->string('JS');
        $table->integer('Quantity');
        $table->integer('Amount');
        $table->string('PCode');
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
      Schema::drop('penawaran');
    }
}
