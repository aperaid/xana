<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('retur', function (Blueprint $table) {
        $table->increments('id');
        $table->string('ReturCode')->unique();
        $table->string('Tgl');
        $table->string('Transport')->default(0);
        $table->string('PesanCode')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('retur');
    }
}
