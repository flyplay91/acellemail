<?php

namespace Acelle\Jobs;

use Acelle\Library\Log as MailLog;

class CampaignJob extends SystemJob
{   
    /**
     * Create a new job instance.
     * @note: Parent constructors are not called implicitly if the child class defines a constructor.
     *        In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
}
