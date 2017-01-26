<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePocustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('pocustomer', function (Blueprint $table) {
        $table->increments('id');
        $table->string('Reference')->unique();
        $table->string('Tgl');
        $table->string('PCode');
        $table->integer('Transport')->default(0);
        $table->integer('PPNT')->default(0);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('pocustomer');
    }
}
