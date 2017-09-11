<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePlanSubscriptionSendingServerOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update plan
        foreach (\Acelle\Model\Plan::all() as $plan) {
            $os = json_decode($plan->options, true);
            if (isset($os["create_sending_servers"])) {
                if ($os["create_sending_servers"] == 'yes') {
                    $plan->setOption('sending_server_option','own');
                } else {
                    $plan->setOption('sending_server_option','system');
                }
            }
        }

        // Update subscription
        foreach (\Acelle\Model\Subscription::all() as $subscription) {
            $os = json_decode($subscription->options, true);
            if (isset($os["create_sending_servers"])) {
                if ($os["create_sending_servers"] == 'yes') {
                    $subscription->setOption('sending_server_option','own');
                } else {
                    $subscription->setOption('sending_server_option','system');
                }
            }
        }
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
