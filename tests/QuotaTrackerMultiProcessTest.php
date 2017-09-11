<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Acelle\Library\QuotaTrackerFile;
use Acelle\Model\Customer;
use Acelle\Model\SendingServer;
use Carbon\Carbon;

class QuotaTrackerMultiProcessTest extends TestCase
{
    /**
     * Test if the quota tracking system itself is correct
     *
     * @return void
     */
    public function testRaceCondition()
    {
        $file = '/tmp/quotatest';
        $tracker = new QuotaTrackerFile($file, null, ['1 hour' => 100000]);
        $tracker->reset();

        $this->assertTrue(empty($tracker->getSeries()));

        $parentPid = getmypid();
        $children = [];
        for ($i = 1; $i <= 3; ++$i) {
            $pid = pcntl_fork();

            // for child process only
            if (!$pid) {
                sleep(1);

                $tracker = new QuotaTrackerFile($file, null, ['1 hour' => 100000]);

                $result = $this->nTimes(300, $tracker, function($tracker) {
                    return $tracker->add(Carbon::now());
                });

                exit($i + 1);
                // end child process
            } else {
                $children[] = $pid;
            }
        }

        // wait for child processes to finish
        foreach($children as $child) {
            $pid = pcntl_wait($status);
            $this->assertTrue(pcntl_wifexited($status));
        }

        $this->assertTrue(sizeof($tracker->getSeries()) == 900);
    }

    /**
     * Test User quota usage
     */
    public function testUserQuotaUsage()
    {
        // setup assumptions
        $lock = storage_path("app/user/quota/dummy");
        if (file_exists($lock)) {
            unlink($lock);
        }

        $user = $this->getMockBuilder(Customer::class)
                     ->setMethods(['getQuotaHash', 'getSendingLimits', 'getSendingQuotaLockFile', 'getCurrentSubscription'])
                     ->disableOriginalConstructor()
                     ->disableOriginalClone()
                     ->disableArgumentCloning()
                     ->disallowMockingUnknownTypes()
                     ->getMock();

        // assume that REDIS is not enabled (then QuotaTrackerStd is used)
        // and user quota is: 3 emails every 1 minute
        //\Config::shouldReceive('get')
        //            ->once()
        //            ->with('app.redis_enabled')
        //            ->andReturn(false);

        $user->method('getSendingQuotaLockFile')->willReturn($lock);
        $user->method('getQuotaHash')->willReturn(['start' => (new Carbon('first day of March 2016'))->timestamp, 'max' => 1000000]);
        $user->method('getSendingLimits')->willReturn(['1 minute' => 3]);
        $user->method('getCurrentSubscription')->willReturn(true);

        // TEST IF QUOTA IS CORRECTLY ENFORCED
        // no usage yet
        $this->assertFalse($user->overQuota());
        $this->assertTrue($user->getQuotaTracker()->getUsage() == 0);

        // use 3 slots out of 3 slots allowed -> ok
        $this->assertTrue($user->countUsage());
        $this->assertTrue($user->getQuotaTracker()->getUsage() == 1);
        $this->assertTrue($user->countUsage());
        $this->assertTrue($user->getQuotaTracker()->getUsage() == 2);
        $this->assertTrue($user->countUsage());
        $this->assertTrue($user->getQuotaTracker()->getUsage() == 3);

        // use extra slot -> failed
        $this->assertTrue($user->overQuota());
        $this->assertTrue($user->getQuotaTracker()->getUsage() == 3);
        $this->assertTrue($user->countUsage());
        $this->assertTrue($user->getSendingQuotaUsage() == 4);
        //$this->assertTrue((float) $user->getSendingQuotaUsagePercentage() == (float) 100 );

        sleep(61);
        // more than 1 minute has passed, now OKIE
        $this->assertFalse($user->overQuota());

        // TEST IF THE TIME SERIES IS CORRECTLY RECORDED
        $t1 = Carbon::now();
        $user->countUsage($t1);
        sleep(20);
        $t2 = Carbon::now();
        $user->countUsage($t2);

        // now $t1 is the first point of the time series
        // cleanup will count the time series from 'start', so calling it here does not have any impact on the series
        $user->getQuotaTracker()->cleanupSeries();
        $this->assertFalse($user->getQuotaTracker()->getSeries()[0] == $t1->timestamp);

        // to test it, cut-off from $t1
        $user->getQuotaTracker()->cleanupSeries(Carbon::now(), '1 minute');
        $this->assertTrue($user->getQuotaTracker()->getSeries()[0] == $t1->timestamp);

        sleep(50);
        // now $t2 becomes the first point of the time series
        $user->getQuotaTracker()->cleanupSeries(Carbon::now(), '1 minute');
        $this->assertTrue($user->getQuotaTracker()->getSeries()[0] == $t2->timestamp);
    }

    /**
     * Test User quota usage
     */
    public function testSendingServerQuotaUsage()
    {
        // setup assumptions
        $lock = storage_path("app/server/quota/dummy");
        if (file_exists($lock)) {
            unlink($lock);
        }

        // setup assumptions
        $server = $this->getMockBuilder(SendingServer::class)
                     ->setMethods(['getQuotaIntervalString', 'getSendingQuota', 'getSendingQuotaLockFile'])
                     ->disableOriginalConstructor()
                     ->disableOriginalClone()
                     ->disableArgumentCloning()
                     ->disallowMockingUnknownTypes()
                     ->getMock();

        // assume that REDIS is not enabled (then QuotaTrackerStd is used)
        // and user quota is: 3 emails every 1 minute
        //\Config::shouldReceive('get')
        //            ->once()
        //            ->with('app.redis_enabled')
        //            ->andReturn(false);

        $server->created_at = new Carbon('1 month ago');
        $server->method('getQuotaIntervalString')->willReturn('1 minute');
        $server->method('getSendingQuotaLockFile')->willReturn($lock);
        $server->method('getSendingQuota')->willReturn('3');

        // TEST IF QUOTA IS CORRECTLY ENFORCED
        // no usage yet
        $this->assertFalse($server->overQuota());
        $this->assertTrue($server->getQuotaTracker()->getUsage() == 0);

        // use 3 slots out of 3 slots allowed -> ok
        $this->assertTrue($server->countUsage());
        $this->assertTrue($server->getQuotaTracker()->getUsage() == 1);
        $this->assertTrue($server->countUsage());
        $this->assertTrue($server->getQuotaTracker()->getUsage() == 2);
        $this->assertTrue($server->countUsage());
        $this->assertTrue($server->getQuotaTracker()->getUsage() == 3);

        // use extra slot -> failed
        $this->assertTrue($server->overQuota());
        $this->assertTrue($server->getQuotaTracker()->getUsage() == 3);
        $this->assertTrue($server->countUsage());
        $this->assertTrue($server->getSendingQuotaUsage() == 4);
        //$this->assertTrue((float) $server->getSendingQuotaUsagePercentage() == (float) 100 );

        sleep(61);
        // more than 1 minute has passed, now OKIE
        $this->assertFalse($server->overQuota());

        // TEST IF THE TIME SERIES IS CORRECTLY RECORDED
        $t1 = Carbon::now();
        $server->countUsage($t1);
        sleep(20);
        $t2 = Carbon::now();
        $server->countUsage($t2);

        $server->getQuotaTracker()->cleanupSeries(null, '1 minute');
        // now $t1 is the first point of the time series
        $this->assertTrue($server->getQuotaTracker()->getSeries()[0] == $t1->timestamp);

        sleep(50);
        $server->getQuotaTracker()->cleanupSeries(null, '1 minute');
        // now $t2 becomes the first point of the time series
        $this->assertTrue($server->getQuotaTracker()->getSeries()[0] == $t2->timestamp);
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
            //sleep(1);
        }
        return $success;
    }
}
