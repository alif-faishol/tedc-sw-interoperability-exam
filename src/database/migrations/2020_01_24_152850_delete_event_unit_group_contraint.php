<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteEventUnitGroupContraint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events_unit_groups', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events_unit_groups', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->foreign('event_id')->references('id')->on('events')->change();
        });
    }
}
