<?php

namespace Acelle\Jobs;

use Acelle\Library\Log as MailLog;

class UpdateAutomationJob extends SystemJob
{
    protected $automation;

    /**
     * Create a new job instance.
     * @note: Parent constructors are not called implicitly if the child class defines a constructor.
     *        In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * @return void
     */
    public function __construct($automation)
    {
        $this->automation = $automation;
        parent::__construct();

        // This line must go after the constructor
        $this->linkJobToAutomation();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function linkJobToAutomation()
    {
        $systemJob = $this->getSystemJob();
        $systemJob->data = $this->automation->id;
        $systemJob->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->automation->updateCache();
    }
}
