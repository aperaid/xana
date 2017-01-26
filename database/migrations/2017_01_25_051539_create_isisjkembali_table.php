<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIsisjkembaliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('isisjkembali', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('IsiSJKem')->unique();
        $table->string('Warehouse')->nullable();
        $table->integer('QTertanda');
        $table->integer('QTerima')->default(0);
        $table->integer('Purchase');
        $table->string('SJKem');
        $table->integer('Periode');
        $table->integer('IsiSJKir');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('isisjkembali');
    }
}
