<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixAutomationTriggerSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\Acelle\Model\AutoEvent::where('event_type', '=', 'subscriber-event')->get() as $e) {
            $data = $e->getData();
            $updated = false;
            if (!array_key_exists('at', $data)) {
                $updated = true;
                $data['at'] = '00:00';
            }

            if ($data['delay_unit'] == 'hour' || $data['delay_unit'] == 'year') {
                $updated = true;
                $data['delay_unit'] = 'day';
            }

            if ($updated) {
                $e->data = json_encode($data);
                $e->save();
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
