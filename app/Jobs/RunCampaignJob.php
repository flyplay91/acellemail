<?php

namespace Acelle\Jobs;

use Acelle\Library\Log as MailLog;

class RunCampaignJob extends CampaignJob
{
    protected $campaign;

    /**
     * Create a new job instance.
     * @note: Parent constructors are not called implicitly if the child class defines a constructor.
     *        In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     * 
     * @return void
     */
    public function __construct($campaign)
    {
        $this->campaign = $campaign;
        parent::__construct();

        // This line must go after the constructor
        $this->linkJobToCampaign();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function linkJobToCampaign()
    {
        $systemJob = $this->getSystemJob();
        $systemJob->data = $this->campaign->id;
        $systemJob->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // start campaign
        $this->campaign->start();
    }
}
