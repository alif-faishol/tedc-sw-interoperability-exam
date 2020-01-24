<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservedEventTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserved_event_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('unit_id')->index('unit_id_foreign');
            $table->bigInteger('event_id')->index('event_id_foreign');
            $table->bigInteger('user_id')->index('user_id_foreign');
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
        Schema::dropIfExists('reserved_event_tickets');
    }
}
