<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForeignKeysConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->bigInteger('tenant_user_id')->unsigned()->change();
            $table->foreign('tenant_user_id')->references('id')->on('users');
        });
        Schema::table('events_unit_groups', function (Blueprint $table) {
            $table->bigInteger('event_id')->unsigned()->change();
            $table->bigInteger('unit_group_id')->unsigned()->change();
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('unit_group_id')->references('id')->on('unit_groups');
        });
        Schema::table('properties', function (Blueprint $table) {
            $table->bigInteger('owner_user_id')->unsigned()->change();
            $table->foreign('owner_user_id')->references('id')->on('users');
        });
        Schema::table('reserved_event_tickets', function (Blueprint $table) {
            $table->bigInteger('unit_id')->unsigned()->change();
            $table->bigInteger('event_id')->unsigned()->change();
            $table->bigInteger('user_id')->unsigned()->change();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('reserved_units', function (Blueprint $table) {
            $table->bigInteger('unit_id')->unsigned()->change();
            $table->bigInteger('tenant_user_id')->unsigned()->change();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('tenant_user_id')->references('id')->on('users');
        });
        Schema::table('units', function (Blueprint $table) {
            $table->bigInteger('property_id')->unsigned()->change();
            $table->bigInteger('unit_group_id')->unsigned()->change();
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('unit_group_id')->references('id')->on('unit_groups');
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
