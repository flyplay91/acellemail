<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Acelle\Library\QuotaTrackerFile;
use Carbon\Carbon;

class QuotaTrackerFileArchiedTest extends TestCase
{
    /**
     * Test if the quota tracking system itself is correct
     *
     * @return void
     */
    public function testQuotaArchiving()
    {
        $file = '/tmp/quotatest';

        /**
         * Basic test cases
         */
        $tracker = new QuotaTrackerFile($file, ['start' => (new Carbon('1 month ago'))->timestamp, 'max' => -1], []);
        $tracker->reset();

        $tracker->add(new Carbon('3 months ago')); // before the start time
        $tracker->add(new Carbon('2 months ago')); // before the start time
        $tracker->add(new Carbon('7 days ago')); // after the start time

        $this->assertTrue($tracker->getUsage() == 1);

        $tracker->cleanupSeries();
        $tracker->cleanupSeries();
        $tracker->cleanupSeries();
        $this->assertTrue($tracker->getUsage() == 1);

        list($start, $value) = $tracker->getRawArchived();
        $this->assertTrue($value == 0);

        /**
         * Basic test cases with archived data
         *
         */
        $tracker = new QuotaTrackerFile($file, ['start' => (new Carbon('1 month ago'))->timestamp, 'max' => -1], []);
        $tracker->reset();

        $tracker->add(new Carbon('4 months ago')); // before the start time
        $tracker->add(new Carbon('3 months ago')); // before the start time
        $tracker->add(new Carbon('7 days ago')); // after the start time
        $tracker->add(new Carbon('6 days ago')); // after the start time
        $tracker->add(new Carbon('5 days ago')); // after the start time
        $tracker->add(new Carbon('4 days ago')); // after the start time
        $tracker->add(new Carbon('3 days ago')); // after the start time
        $tracker->add(new Carbon('2 days ago')); // after the start time
        $tracker->add(new Carbon('1 days ago')); // after the start time

        $this->assertTrue($tracker->getUsage() == 7);
        $tracker->cleanupSeries(); // default 1 month
        $this->assertTrue($tracker->getUsage() == 7);
        list($start, $value) = $tracker->getRawArchived();
        $this->assertTrue($value == 0);

        sleep(2); // in order to cover the last 2 day only
        $tracker->cleanupSeries(null, '3 days'); // default 1 month
        $this->assertTrue($tracker->getUsage() == 7);
        list($start, $value) = $tracker->getRawArchived();
        $this->assertTrue($value == 5);
        $this->assertTrue(sizeof($tracker->getSeries()) == 2);

        /**
         * Series data remains unchanged in certain cases
         *
         */
        $before = file_get_contents($file);
        $tracker->cleanupSeries(null, '2 months'); // default 1 month
        $after = file_get_contents($file);
        $this->assertTrue($before == $after);
        $tracker->cleanupSeries(null, '20 days'); // default 1 month
        $after = file_get_contents($file);
        $this->assertTrue($before == $after); // already archived above with interval = '3 days', no change now
        $tracker->cleanupSeries(null, '2 days'); // default 1 month
        $after = file_get_contents($file);
        $this->assertTrue($before != $after);

        /**
         * Series data remains unchanged in certain cases
         *
         */
        $tracker = new QuotaTrackerFile($file, ['start' => (new Carbon('2 months ago'))->timestamp, 'max' => -1], []);
        $tracker->cleanupSeries();
        $this->assertTrue($tracker->getUsage() == 7);

        $tracker = new QuotaTrackerFile($file, ['start' => (new Carbon('20 days ago'))->timestamp, 'max' => -1], []);
        $this->assertTrue($tracker->getUsage() == 7);
        $tracker->cleanupSeries();
        $this->assertTrue($tracker->getUsage() == 1);
    }
}
