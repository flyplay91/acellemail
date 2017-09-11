<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertPaddleToPaymentMethods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert some stuff
        DB::table('payment_methods')->insert(
            array(
                'uid' => uniqid(),
                'name' => 'Pay by Card (Paddle)',
                'type' => \Acelle\Model\PaymentMethod::TYPE_PADDLE_CARD,
                'status' => \Acelle\Model\PaymentMethod::STATUS_INACTIVE,
                'custom_order' => 5,
                'created_at' => '2017-08-14 00:00:00',
                'updated_at' => '2017-08-14 00:00:00',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Insert some stuff
        DB::table('payment_methods')->where('type', '=', \Acelle\Model\PaymentMethod::TYPE_PADDLE_CARD)->delete();
    }
}
