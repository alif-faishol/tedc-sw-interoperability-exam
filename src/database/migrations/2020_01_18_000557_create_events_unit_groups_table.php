<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsUnitGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_unit_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('event_id')->index('event_id_foreign');
            $table->bigInteger('unit_group_id')->index('unit_group_id_foreign');
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
        Schema::dropIfExists('events_unit_groups');
    }
}
