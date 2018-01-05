<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseinvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('purchaseinvoice', function (Blueprint $table) {
        $table->increments('id');
        $table->string('PurchaseInvoice');
        $table->string('Tgl');
        $table->string('PesanCode');
        $table->integer('Discount')->default(0);
        $table->text('Catatan')->nullable();
        $table->integer('Lunas')->default(0);
				$table->text('TglTerima')->nullable();
				$table->integer('Termin')->default(0);
        $table->integer('Pembulatan')->default(0);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
