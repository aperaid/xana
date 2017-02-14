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
				$table->integer('PPN')->default(0);
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
        $table->string('Customer2')->nullable();
        $table->string('CustPhone2')->nullable();
        $table->string('CustEmail2')->nullable();
        $table->string('Customer3')->nullable();
        $table->string('CustPhone3')->nullable();
        $table->string('CustEmail3')->nullable();
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
