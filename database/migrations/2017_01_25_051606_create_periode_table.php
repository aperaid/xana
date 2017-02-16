<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('periode', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('Periode');
        $table->string('S');
        $table->string('E');
        $table->integer('Quantity')->default(0);
        $table->integer('IsiSJKir');
        $table->string('SJKem')->nullable();
        $table->string('Reference');
        $table->integer('Purchase');
        $table->integer('Claim')->nullable();
        $table->string('Deletes');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('periode');
    }
}
