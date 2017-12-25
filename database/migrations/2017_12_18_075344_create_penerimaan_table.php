<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenerimaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('penerimaan', function (Blueprint $table) {
        $table->increments('id');
        $table->string('TerimaCode')->unique();
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
      Schema::drop('penerimaan');
    }
}
