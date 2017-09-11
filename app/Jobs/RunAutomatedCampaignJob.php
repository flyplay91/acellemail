<?php

namespace Acelle\Jobs;

use Acelle\Library\Log as MailLog;

class RunAutomatedCampaignJob extends CampaignJob
{
    protected $campaign;
    protected $trigger;
    protected $subscribers;

    /**
     * Create a new job instance.
     * @note: Parent constructors are not called implicitly if the child class defines a constructor.
     *        In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     * 
     * @return void
     */
    public function __construct($campaign, $trigger, $subscribers = null)
    {
        $this->campaign = $campaign;
        $this->trigger = $trigger;
        $this->subscribers = $subscribers;
        parent::__construct();

        // This line must go after the constructor
        $this->linkJobToTrigger();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function linkJobToTrigger()
    {
        $systemJob = $this->getSystemJob();
        $systemJob->data = $this->trigger->id;
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
        $this->campaign->start($this->trigger, $this->subscribers);
    }
}