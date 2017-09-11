<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteListSegmentColsFromAutomations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automations', function (Blueprint $table) {
            $table->dropForeign('automations_mail_list_id_foreign');
            $table->dropColumn('mail_list_id');
            
            $table->dropForeign('automations_segment_id_foreign');
            $table->dropColumn('segment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automations', function (Blueprint $table) {
            $table->integer('mail_list_id')->unsigned()->nullable();
            $table->integer('segment_id')->unsigned()->nullable();
            
            $table->foreign('mail_list_id')->references('id')->on('mail_lists')->onDelete('cascade');            
            $table->foreign('segment_id')->references('id')->on('segments')->onDelete('cascade');
        });
    }
}
