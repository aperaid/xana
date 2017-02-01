<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('invoice', function (Blueprint $table) {
        $table->increments('id');
        $table->string('Invoice');
        $table->string('JSC');
        $table->string('Tgl');
        $table->string('Reference');
        $table->integer('Periode');
        $table->integer('PPN');
        $table->integer('Discount')->nullable();
        $table->text('Catatan')->nullable();
        $table->integer('Lunas');
        $table->integer('Count');
        $table->integer('Times');
        $table->integer('TimesKembali');
        $table->integer('Pembulatan')->nullable();
        $table->integer('TransportInvoice')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('invoice');
    }
}
