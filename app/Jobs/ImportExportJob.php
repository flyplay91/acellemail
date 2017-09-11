<?php

namespace Acelle\Jobs;

class ImportExportJob extends SystemJob
{
    protected $mailList;
    protected $customer;
    protected $admin;
    protected $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mailList=NULL, $customer=NULL, $admin=NULL)
    {
        // call parent's constructor
        parent::__construct();

        $this->mailList = $mailList;
        $this->customer = $customer;
        $this->admin = $admin;
        $systemJob = $this->getSystemJob();

        // folder for job file
        $path = storage_path('job/');

        // mkdir if not exist
        if (!file_exists($path)) {
            $oldmask = umask(0);
            mkdir($path, 0775, true);
            umask($oldmask);
        }

        // mkdir if not exist
        $path = $path.$systemJob->id."/";
        if (!file_exists($path)) {
            $oldmask = umask(0);
            \Acelle\Library\Tool::xdelete($path);
            mkdir($path, 0775, true);
            umask($oldmask);
        }

        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

    /**
     * Get job files path.
     *
     * @return string file path
     */
    public function getPath() {
        return $this->path;
    }
}
