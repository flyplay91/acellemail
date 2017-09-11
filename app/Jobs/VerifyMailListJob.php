<?php

namespace Acelle\Jobs;

use Acelle\Library\Log as MailLog;
use Acelle\Model\MailList;

class VerifyMailListJob extends SystemJob
{
    protected $mailListId;
    protected $serverId;

    /**
     * Create a new job instance.
     * @note: Parent constructors are not called implicitly if the child class defines a constructor.
     *        In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * @return void
     */
    public function __construct($mailListId, $serverId)
    {
        $this->mailListId = $mailListId;
        $this->serverId = $serverId;

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
        $systemJob->data = $this->mailListId;
        $systemJob->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // start verification process
            $list = MailList::find($this->mailListId);
            $list->runVerification($this->serverId);
        } catch (\Exception $e) {
            throw $e;
            MailLog::warning(sprintf('Verification process for list `%s` failed. %s', $list->id, $e->getMessage()));
        }

    }
}
