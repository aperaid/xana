<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenerimaanlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::create('penerimaanlist', function (Blueprint $table) {
				$table->increments('id');
        $table->integer('QTerima');
        $table->string('TerimaCode');
        $table->integer('idPesanList');
			});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('penerimaanlist');
    }
}
