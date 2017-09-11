<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoTriggerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_triggers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('auto_event_id')->unsigned();
            $table->integer('subscriber_id')->unsigned()->nullable();
            $table->integer('preceded_by')->unsigned()->nullable();
            $table->timestamp('start_at')->nullable();

            $table->timestamps();
            
            $table->foreign('auto_event_id')->references('id')->on('auto_events')->onDelete('cascade');
            $table->foreign('preceded_by')->references('id')->on('auto_triggers')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auto_triggers');
    }
}
