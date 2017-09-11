<?php

namespace Acelle\Jobs;

use Acelle\Library\Log as MailLog;

class UpdateUserJob extends SystemJob
{
    protected $customer;

    /**
     * Create a new job instance.
     * @note: Parent constructors are not called implicitly if the child class defines a constructor.
     *        In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * @return void
     */
    public function __construct($customer)
    {
        $this->customer = $customer;
        parent::__construct();

        // This line must go after the constructor
        $this->linkJobToUser();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function linkJobToUser()
    {
        $systemJob = $this->getSystemJob();
        $systemJob->data = $this->customer->id;
        $systemJob->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->customer->updateCache();
    }
}
