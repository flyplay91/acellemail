<?php

namespace Acelle\Jobs;

class UpdateSegmentJob extends SystemJob
{
    protected $segment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($segment)
    {
        $this->segment = $segment;
        parent::__construct();

        // This line must go after the constructor
        $this->linkJobToSegment();
    }

    /**
     * Associate the segment id to job
     *
     * @return void
     */
    public function linkJobToSegment()
    {
        $systemJob = $this->getSystemJob();
        $systemJob->data = $this->segment->id;
        $systemJob->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->segment->updateCache();
    }
}
