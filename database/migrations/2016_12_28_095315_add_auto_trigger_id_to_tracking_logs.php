<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoTriggerIdToTrackingLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_logs', function (Blueprint $table) {
            $table->integer('auto_trigger_id')->unsigned()->nullable();

            $table->foreign('auto_trigger_id')->references('id')->on('auto_triggers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_logs', function (Blueprint $table) {
            $table->dropForeign('tracking_logs_auto_trigger_id_foreign');
            $table->dropColumn('auto_trigger_id');
        });
    }
}
