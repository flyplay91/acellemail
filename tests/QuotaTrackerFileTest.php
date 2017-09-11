<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Acelle\Library\QuotaTrackerFile;
use Carbon\Carbon;

class QuotaTrackerFileTest extends TestCase
{
    /**
     * Test if the quota tracking system itself is correct
     *
     * @return void
     */
    public function testOverQuota()
    {
        $file = '/tmp/quotatest';

        /**
         * Suppose the quota setting is: {6 emails, every 5 seconds}
         * Then we test with: send an email every 1 second and and expect it will NOT to exceed the quota
         */
        $tracker = new QuotaTrackerFile($file, null, ['5 seconds' => 6]);
        $tracker->reset();

        $result = $this->nTimes(10, $tracker, function($tracker) {
            return $tracker->add(Carbon::now());
        });

        $this->assertTrue($result);

        /**
         * Suppose the quota setting is: {6 emails, every 5 seconds}
         * Then we test with: send an email every 1 second and and expect it will NOT to exceed the quota
         */
        $tracker = new QuotaTrackerFile($file, ['start' => (new Carbon('first day of March 2016'))->timestamp, 'max' => 9], ['5 seconds' => 6]);
        $tracker->reset();

        $result = $this->nTimes(9, $tracker, function($tracker) {
            return $tracker->add(Carbon::now());
        });

        $this->assertTrue($result);
        $this->assertFalse($tracker->check(Carbon::now()));

        /**
         * Suppose the quota setting is: {5 emails, every 4 seconds}
         * Then we test with: send an email every 1 second and and expect it will exceed the quota
         */
        $tracker = new QuotaTrackerFile($file, null, ['5 second' => 4]);
        $tracker->reset();
        $result = $this->nTimes(5, $tracker, function($tracker) {
            $tracker->add(Carbon::now());
            return $tracker->check();
        });
        $this->assertFalse($result);

        /**
         * Suppose the quota setting is: {0 emails, every 1 hours}
         * Then we test with: send an email every 1 second and and expect it to fail immediately at first try
         */
        $tracker = new QuotaTrackerFile($file, null, ['1 hour' => 0]);
        $tracker->reset();
        $result = $this->nTimes(1, $tracker, function($tracker) {
            $tracker->add(Carbon::now());
            $tracker->check();
        });
        $this->assertFalse($result);

        /**
         * Suppose the quota setting is: {1 emails, every 1 day}
         * Then we test with: send an email every 1 second and and expect it NOT to fail after 1st try
         */
        $tracker = new QuotaTrackerFile($file, null, ['1 days' => 1]);
        $tracker->reset();
        $result = $this->nTimes(1, $tracker, function($tracker) {
            return $tracker->add(Carbon::now());
        });
        $this->assertTrue($result);

        /**
         * Suppose the quota setting is: {1 emails, every 1 day}
         * Then we test with: send an email every 1 second and and expect it to fail after 2nd try
         */
        $tracker = new QuotaTrackerFile($file, null, ['1 days' => 1]);
        $tracker->reset();
        $result = $this->nTimes(2, $tracker, function($tracker) {
            $tracker->add(Carbon::now());
            return $tracker->check();
        });
        $this->assertFalse($result);
    }

    /**
     * Test cleanupSeries() function
     *
     * @return void
     */
    public function testQuotaCleanup()
    {
        $file = '/tmp/quotatest';

        $tracker = new QuotaTrackerFile($file, ['start' => (new Carbon('1 month ago'))->timestamp, 'max' => 1000]);
        $tracker->reset();

        $tracker->add();
        $tracker->add();

        $this->assertTrue(sizeof($tracker->getRawSeries()) == 2);
        sleep(2);
        $tracker = new QuotaTrackerFile($file, ['start' => (new Carbon('1 month ago'))->timestamp, 'max' => 1000]);
        $this->assertTrue($tracker->getUsage() == 2);
        $this->assertTrue(sizeof($tracker->getRawSeries()) == 2);
        $tracker->cleanupSeries();
        $this->assertTrue($tracker->getUsage() == 2);
        $this->assertTrue(sizeof($tracker->getRawSeries()) == 2);
        $tracker = new QuotaTrackerFile($file, ['start' => Carbon::now()->timestamp, 'max' => 1000]);
        sleep(2);
        $this->assertTrue($tracker->getUsage() == 0);
        $this->assertTrue(sizeof($tracker->getRawSeries()) == 2);
        $tracker->add();
        $this->assertTrue($tracker->getUsage() == 1);
        $this->assertTrue(sizeof($tracker->getRawSeries()) == 3);
        $tracker->cleanupSeries();
        $this->assertTrue($tracker->getUsage() == 1);
        $this->assertTrue(sizeof($tracker->getRawSeries()) == 1);
    }

    /**
     * Simulate an activity every second
     *
     * @return void
     */
    private function nTimes($try, $tracker, $func) {
        $success = true;
        for($i = 0; $i < $try; $i++) {
            $success = $func($tracker);
            if (!$success) {
                return false;
            }
            sleep(1);
        }
        return $success;
    }
}
