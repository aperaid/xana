<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIsisjkirimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('isisjkirim', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('IsiSJKir')->unique();
        $table->integer('QKirim');
        $table->integer('QTertanda')->default(0);
        $table->integer('QSisaKemInsert')->default(0);
        $table->integer('QSisaKem')->default(0);
        $table->string('Warehouse');
        $table->integer('Purchase');
        $table->string('SJKir');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('isisjkirim');
    }
}
