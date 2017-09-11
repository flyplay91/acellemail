<?php

namespace Acelle\Jobs;

use Acelle\Library\Log as MailLog;

class UpdateMailListJob extends SystemJob
{
    protected $list;

    /**
     * Create a new job instance.
     * @note: Parent constructors are not called implicitly if the child class defines a constructor.
     *        In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * @return void
     */
    public function __construct($list)
    {
        $this->list = $list;
        parent::__construct();

        // This line must go after the constructor
        $this->linkJobToMailList();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function linkJobToMailList()
    {
        $systemJob = $this->getSystemJob();
        $systemJob->data = $this->list->id;
        $systemJob->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->list->updateCachedInfo();
    }
}
