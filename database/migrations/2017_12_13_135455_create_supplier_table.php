<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('supplier', function (Blueprint $table) {
        $table->increments('id');
        $table->string('SCode')->unique();
        $table->string('Company');
        $table->string('Supplier')->nullable();
        $table->text('CompAlamat')->nullable();
        $table->string('CompZip')->nullable();
        $table->string('CompKota')->nullable();
        $table->string('CompPhone')->nullable();
        $table->string('CompEmail')->nullable();
        $table->string('SupPhone')->nullable();
        $table->string('SupEmail')->nullable();
        $table->string('Fax')->nullable();
        $table->string('NPWP')->nullable();
        $table->string('Supplier2')->nullable();
        $table->string('SupPhone2')->nullable();
        $table->string('SupEmail2')->nullable();
        $table->string('Supplier3')->nullable();
        $table->string('SupPhone3')->nullable();
        $table->string('SupEmail3')->nullable();
        $table->integer('Count')->default(1);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('supplier');
    }
}
