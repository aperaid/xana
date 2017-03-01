<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('po', function (Blueprint $table) {
        $table->increments('id');
        $table->string('POCode')->unique();
        $table->string('Tgl');
				$table->integer('Periode');
        $table->text('Catatan')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('po');
    }
}
