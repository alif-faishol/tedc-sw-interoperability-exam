<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservedUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserved_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tenant_user_id')->index('tenant_user_id_foreign');
            $table->bigInteger('unit_id')->index('unit_id_foreign');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reserved_units');
    }
}
