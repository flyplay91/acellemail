<?php

namespace Acelle\Jobs;

use Acelle\Library\Log as CustomLog;

class ImportBlacklistJob extends ImportExportJob
{
    // @todo this should better be a constant
    protected $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file, $customer=NULL, $admin=NULL)
    {
        // call parent's constructor
        parent::__construct(NULL, $customer, $admin);

        // Upload csv
        $this->file = join_paths($this->path, 'data.txt');
        rename($file, $this->file);
        chmod($this->file, 0775);

        // Update system job status
        // init the status
        $this->updateStatus([
            'customer_id' => (isset($customer) ? $customer->id : NULL),
            'admin_id' => (isset($admin) ? $admin->id : NULL),
            'status' => \Acelle\Model\Blacklist::IMPORT_STATUS_NEW,
            'error_message' => '',
            'total' => 0,
            'processed' => 0,
        ]);
    }

    /**
     * Get import file name.
     *
     * @return string file path
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get import file name.
     *
     * @return void
     */
    public function updateStatus($data)
    {
        $systemJobModel = $this->getSystemJob();
        $json = ($systemJobModel->data) ? json_decode($systemJobModel->data, true) : [ 'log' => $this->getLog() ];
        $systemJobModel->data = json_encode(array_merge($json, $data));
        $systemJobModel->save();
    }

    /**
     * Get import log file
     *
     * @return string file path
     */
    public function getLog()
    {
        return join_paths($this->getPath(), 'import.log');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Acelle\Model\Blacklist::import($this->file, $this, $this->customer, $this->admin);
    }

    /**
     * Get the job's logger
     *
     * @return object job logger
     */
    public function getLogger()
    {
        $log_name = 'importer';
        $logger = CustomLog::create( $this->getLog(), $log_name );
        return $logger;
    }
}
