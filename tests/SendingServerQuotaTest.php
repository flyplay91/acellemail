<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Acelle\Model\SendingServer;
use Carbon\Carbon;

class SendingServerQuotaTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testQuotaCheck()
    {
        // setup assumptions
        $server = $this->getMockBuilder(SendingServer::class)
                     ->setMethods(['getQuotaIntervalString', 'getSendingQuota', 'renewQuotaTracker', 'save', 'getSendingQuotaLockFile'])
                     ->disableOriginalConstructor()
                     ->disableOriginalClone()
                     ->disableArgumentCloning()
                     ->disallowMockingUnknownTypes()
                     ->getMock();

        // assume that REDIS is not enabled (then QuotaTrackerStd is used)
        // and server quota is: 3 emails every 1 minute
        //\Config::shouldReceive('get')
        //            ->once()
        //            ->with('app.redis_enabled')
        //            ->andReturn(false);

        $server->created_at = new Carbon('1 month ago');
        $server->method('save')->willReturn(true);
        $server->method('getQuotaIntervalString')->willReturn('1 minute');
        $server->method('getSendingQuota')->willReturn('3');
        $server->method('getSendingQuotaLockFile')->willReturn('/tmp/' . uniqid());

        // TEST IF QUOTA IS CORRECTLY ENFORCED
        // no usage yet
        $this->assertFalse($server->overQuota());

        // use 3 slots out of 3 slots allowed -> ok
        $this->assertTrue($server->countUsage());
        $this->assertTrue($server->countUsage());
        $this->assertTrue($server->countUsage());

        // use extra slot -> failed
        $this->assertTrue($server->overQuota());
        $this->assertTrue($server->countUsage());

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
        $server->overQuota(); // trigger recalculation of time series
        $this->assertTrue($server->getQuotaTracker()->getSeries()[0] == $t2->timestamp);
    }
}
