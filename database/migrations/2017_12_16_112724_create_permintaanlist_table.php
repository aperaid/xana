<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermintaanlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('permintaanlist', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('Quantity');
        $table->integer('Amount');
        $table->string('ICode');
				$table->string('MintaCode');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('permintaanlist');
    }
}
