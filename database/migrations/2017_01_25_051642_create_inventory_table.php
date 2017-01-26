<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('inventory', function (Blueprint $table) {
        $table->increments('id');
        $table->string('Code')->unique();
        $table->string('Barang');
        $table->integer('JualPrice');
        $table->integer('Price');
        $table->string('Type');
        $table->integer('Kumbang')->default(0);
        $table->integer('BulakSereh')->default(0);
        $table->integer('Legok')->default(0);
        $table->integer('CitraGarden')->default(0);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('inventory');
    }
}
