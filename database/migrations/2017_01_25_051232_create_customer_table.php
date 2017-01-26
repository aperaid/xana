<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('customer', function (Blueprint $table) {
        $table->increments('id');
        $table->string('CCode')->unique();
        $table->string('Company');
        $table->string('Customer')->nullable();
        $table->text('CompAlamat')->nullable();
        $table->string('CompZip')->nullable();
        $table->string('CompKota')->nullable();
        $table->string('CompPhone')->nullable();
        $table->string('CompEmail')->nullable();
        $table->string('CustPhone')->nullable();
        $table->string('CustEmail')->nullable();
        $table->string('Fax')->nullable();
        $table->string('NPWP')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('customer');
    }
}
