<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSjkembaliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('sjkembali', function (Blueprint $table) {
        $table->increments('id');
        $table->string('SJKem')->unique();
        $table->string('Tgl');
        $table->string('Reference');
        $table->string('NoPolisi')->nullable();
        $table->string('Sopir')->nullable();
        $table->string('Kenek')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('sjkembali');
    }
}
