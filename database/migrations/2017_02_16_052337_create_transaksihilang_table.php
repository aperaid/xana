<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransaksihilangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('transaksihilang', function (Blueprint $table){
        $table->increments('id');
        $table->string('Tgl');
        $table->integer('QHilang');
        $table->integer('Purchase');
        $table->integer('Periode');
				$table->string('SJType');
        $table->string('SJ');
				$table->text('HilangText')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('transaksihilang');
    }
}
