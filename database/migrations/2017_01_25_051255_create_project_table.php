<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('project', function (Blueprint $table) {
        $table->increments('id');
        $table->string('PCode')->unique();
        $table->string('Project');
        $table->text('ProjAlamat')->nullable();
        $table->string('ProjZip')->nullable();
        $table->string('ProjKota')->nullable();
        $table->string('CCode');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('project');
    }
}
