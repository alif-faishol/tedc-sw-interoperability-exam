<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('description', 65535);
            $table->bigInteger('property_id')->index('property_id_foreign');
            $table->bigInteger('unit_group_id')->index('unit_group_id_foreign');
            $table->boolean('event_only')->default(false);
            $table->bigInteger('hourly_price');
            $table->bigInteger('daily_price');
            $table->bigInteger('monthly_price');
            $table->bigInteger('yearly_price');
            $table->boolean('available_to_public')->default(true);
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
        Schema::dropIfExists('units');
    }
}
