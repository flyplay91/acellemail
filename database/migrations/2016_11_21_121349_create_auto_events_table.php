<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_events', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uid');
            $table->integer('automation_id')->unsigned();
            $table->string('event_type');
            $table->text('data');
            $table->integer('previous_event_id')->unsigned()->nullable();
            $table->integer('custom_order');

            $table->timestamps();
            
            $table->foreign('automation_id')->references('id')->on('automations')->onDelete('cascade');
            $table->foreign('previous_event_id')->references('id')->on('auto_events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auto_events');
    }
}
