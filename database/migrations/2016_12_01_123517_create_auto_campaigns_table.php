<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('auto_event_id')->unsigned();
            $table->integer('campaign_id')->unsigned();
            
            $table->timestamps();
            
            $table->foreign('auto_event_id')->references('id')->on('auto_events')->onDelete('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auto_campaigns');
    }
}
