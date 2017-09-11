<?php

/**
 * SystemJob class.
 *
 * Model class for tracking system jobs.
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use DB;
use Acelle\Model\Job;

class SystemJob extends Model
{
    // status
    const STATUS_NEW = 'new';
    const STATUS_RUNNING = 'running';
    const STATUS_DONE = 'done';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status'
    ];

    /**
     * Set job as started.
     *
     * @var array
     */
    public function setStarted() {
        $this->status = self::STATUS_RUNNING;
        $this->start_at = \Carbon\Carbon::now();
        $this->save();
    }

    /**
     * Set job as finished.
     *
     * @var array
     */
    public function setDone() {
        $this->status = self::STATUS_DONE;
        $this->end_at = \Carbon\Carbon::now();
        $this->save();
    }

    /**
     * Set job as finished.
     *
     * @var array
     */
    public function setFailed() {
        $this->status = self::STATUS_FAILED;
        $this->end_at = \Carbon\Carbon::now();
        $this->save();
    }

    /**
     * Set job status as cancelled
     *
     * @var array
     */
    public function setCancelled() {
        $this->status = self::STATUS_CANCELLED;
        $this->end_at = \Carbon\Carbon::now();
        $this->save();
    }

    /**
     * Run time.
     *
     * @return collect
     */
    public function runTime()
    {
        return gmdate("H:i:s", -$this->updated_at->diffInSeconds($this->created_at, false));
    }

    /**
     * Stop the job as well as delete the Job records
     *
     * @return collect
     */
    public function clear()
    {
        // delete all system_jobs & jobs
        DB::transaction(function() {
            // delete jobs
            $jobs = Job::where('reserved', 0)->get();
            foreach($jobs as $job) {
                $json = json_decode($job->payload, true);
                try {
                    $j = unserialize($json['data']['command']);
                    if ($j->getSystemJob()->id == $this->id) {
                        Job::destroy($job->id);
                    }
                } catch (\Exception $ex) {
                    // delete orphan job
                    Job::destroy($job->id);
                }
            }

            // delete system_jobs
            self::destroy($this->id);
        });
    }

    /**
     * Delete the all (Laravel) job records which have not been reserved
     *
     * @return collect
     */
    public function clearJobs()
    {
        // delete all system_jobs & jobs
        DB::transaction(function() {
            // delete jobs
            $jobs = Job::where('reserved', 0)->get();
            foreach($jobs as $job) {
                $json = json_decode($job->payload, true);
                try {
                    $j = unserialize($json['data']['command']);
                    if ($j->getSystemJob()->id == $this->id) {
                        Job::destroy($job->id);
                    }
                } catch (\Exception $ex) {
                    // delete orphan job
                    Job::destroy($job->id);
                }
            }
        });
    }

    /**
     * Check if system job is cancelled
     *
     * @return boolean
     */
    function isCancelled()
    {
        return $this->status == SystemJob::STATUS_CANCELLED;
    }

    /**
     * Check if system job is new
     *
     * @return boolean
     */
    function isNew()
    {
        return $this->status == SystemJob::STATUS_NEW;
    }

    /**
     * Check if system job is failed
     *
     * @return boolean
     */
    function isFailed()
    {
        return $this->status == SystemJob::STATUS_FAILED;
    }

    /**
     * Check if system job is new
     *
     * @return boolean
     * @todo this method is for the two classes ImportSubscribersJob/ExportSubscribersJob only
     */
    function isRunning()
    {
        $data = json_decode($this->data);
        return !(in_array($data->status, ['failed', 'done']) || $this->status == \Acelle\Model\SystemJob::STATUS_CANCELLED);
    }

    /**
     * Get new pending jobs
     *
     * @return collection
     */
    public static function getNewJobs()
    {
        return self::where('status', '=', self::STATUS_NEW);
    }

    /**
     * Get value from data
     *
     * @return value
     */
    function getData($name)
    {
        $data = json_decode($this->data, true);
        return isset($data[$name]) ? $data[$name] : NULL;
    }
}
