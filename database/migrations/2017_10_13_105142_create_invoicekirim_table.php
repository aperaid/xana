<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicekirimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('invoicekirim', function (Blueprint $table) {
        $table->increments('id');
        $table->string('Invoice');
        $table->string('JSC');
        $table->string('Tgl');
        $table->string('Reference');
        $table->integer('Periode');
        $table->integer('PPN');
        $table->integer('Discount')->default(0);
        $table->text('Catatan')->nullable();
        $table->integer('Lunas');
        $table->integer('Count');
				$table->text('TglTerima')->nullable();
				$table->integer('Termin')->default(0);
        $table->integer('Times');
        $table->integer('TimesKembali');
        $table->integer('Pembulatan')->default(0);
				$table->string('SJKir');
				$table->integer('Abjad');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('invoicekirim');
    }
}
