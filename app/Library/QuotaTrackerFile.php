<?php

/**
 * QuotaTrackerFile class.
 *
 * Provide a data structure for storing and measure quota
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   Acelle Library
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 * @todo separate the time-series and the quota stuffs
 */

namespace Acelle\Library;
use Carbon\Carbon;

class QuotaTrackerFile
{
    protected $file;
    protected $hasLock = false;
    protected $limits;
    protected $quota;
    protected $series;
    protected $archived;

    const TIMEOUT = 60;
    const UNLIMITED = -1;

    /**
     * Constructor, modeling data from a JSON array
     *
     * @param String $file
     * @param Array $limit, for example $limit = ['1 hour' => 50000, '1 minute' => 2000]
     * @param Array $quota, for example $quota = ['start' => 'Jan 1, 2016', 'interval' => '1 month', 'max' => ]
     * @return void
     */
    public function __construct($file, $quota = null, $limits = [])
    {
        $this->file = $file;
        $this->quota = $quota;
        $this->limits = $limits;
        $this->createLogFile();
    }

    /**
     * Create the lock file itself if not exist
     *
     * @return void
     */
    private function createLogFile()
    {
        if (file_exists($this->file)) {
            return true;
        }

        // lock at application level
        $fp = fopen(storage_path('lock'), "r+");
        $start = time();
        while (true) {
            $this->timeout($start);

            if (flock($fp, LOCK_EX | LOCK_NB)) {
                $old = umask(0);

                // create the base directories for log files
                if (!file_exists(dirname($this->file))) {
                    mkdir(dirname($this->file), 0777, true);
                }

                if (!file_exists($this->file)) {
                    // init an empty file if not exist
                    touch($this->file);

                    // change file permisson
                    chmod($this->file, 0777);
                }

                // change umask back to its original value
                umask($old);

                // unlock
                flock($fp, LOCK_UN);
                break;
            }
        }

        fclose($fp);
    }

    /**
     * Check if over quota, add a time point
     *
     * @param Timestamps $timePoint
     *
     * @return void
     */
    public function add(Carbon $timePoint = null) {
        if (!isset($timePoint)) {
            $timePoint = Carbon::now();
        }

        $mode = 'a';
        $this->getExclusiveLock($mode, function(&$writer) use ($timePoint) {
            fwrite($writer, "," . $timePoint->timestamp);
            fflush($writer);
        });

        return true;
    }

    /**
     * Check if over quota
     *
     * @param Timestamps $timePoint
     *
     * @return void
     */
    public function check(Carbon $timePoint = null) {
        if (!isset($timePoint)) {
            $timePoint = Carbon::now();
        }

        $this->getSharedLock(function() use (&$result, $timePoint) {
            $result = true;

            # check quota
            # the trick is that 2nd parameter is null, indicating a quota check
            if (!is_null($this->quota)) {
                if (self::UNLIMITED != $this->quota['max'] && $this->getUsage($timePoint) >= $this->quota['max']) {
                    $result = false;
                    return;
                }
            }

            foreach ($this->limits as $interval => $max) {
                if (self::UNLIMITED == $max) {
                    continue;
                }

                # check limit
                # there are 2 types of usage
                # 1. usage within the last n hour/day/month
                # 2. usage since the beginning (of the quota period)
                if ($this->getUsage($timePoint, $interval) >= $max) {
                    $result = false;
                    break;
                }
            }
        });

        return $result;
    }

    /**
     * Shift the time series until its range fits the new time point
     *
     * @param Timestamps $timePoint
     *
     * @return void
     */
    private function shiftBy(Carbon $timePoint, $interval)
    {
        // @interval MUST be less than 'history_max_length'
        if (empty($this->series)) {
            return [];
        }

        # Conventions:
        # - $timePoint is not null
        # - $interval empty means get the series from the quota start date
        # - $interval not empty means get the series from the interval
        # - If both $interval and $quota empty --> for backward compatibility --> get quota from the first interval of $this->limit
        if (is_null($interval)) {
            if (is_null($this->quota)) {
                $cutOff = $timePoint->copy()->sub(\DateInterval::createFromDateString(array_keys($this->limits)[0]))->timestamp;
            } else {
                $cutOff = $this->quota['start'];
            }
        } else {
            $cutOff = $timePoint->copy()->sub(\DateInterval::createFromDateString($interval))->timestamp;
        }

        // cut off the series from the point
        $offset = null;
        foreach($this->series as $i => $value) {
            if ($value >= $cutOff) {
                $offset = $i;
                break;
            }
        }

        if (is_null($offset)) {
            return [];
        } else {
            return array_slice($this->series, $offset); // including offset
        }
    }

    /**
     * Renew the quota data for the tracker
     * @note this method MUST be wrapped in a transaction
     *
     * @param Array $series
     * @return void
     */
    public function reset()
    {
        $this->getExclusiveLock('r+', function($writer) {
            ftruncate($writer, 0);
        });
    }

    /**
     * Get the first data point of the time series
     *
     * @return Mixed data point
     */
    private function first()
    {
        return (empty($this->series)) ? null : $this->series[0];
    }

    /**
     * Get the last data point of the time series
     *
     * @return Mixed data point
     */
    private function last()
    {
        return (empty($this->series)) ? null : $this->series[sizeof($this->series) - 1];
    }

    /**
     * Count the time series length
     * @transaction-safe
     *
     * @return Integer count
     */
    public function getUsage($timePoint = null, $interval = null)
    {
        $this->getSharedLock(function() use (&$result, $timePoint, $interval) {
            if (is_null($interval)) {
                list($start, $value) = $this->archived;
                $result = $value + sizeof($this->getSeries($timePoint, $interval));
            } else {
                $result = sizeof($this->getSeries($timePoint, $interval));
            }
        });

        return $result;
    }

    /**
     * Get usage percentage
     *
     * @return float
     */
    public function getUsagePercentage($timePoint = null, $interval = null)
    {
        if (is_null($this->quota)) {
            return $this->getUsage($timePoint, $interval) / array_values($this->limits)[0];
        }
        return (float) $this->getUsage($timePoint, $interval) / $this->quota['max'];
    }

    /**
     * Get the data series
     * @transaction-safe
     *
     * @return Array data series
     */
    public function getSeries(Carbon $timePoint = null, $interval = null)
    {
        if (is_null($timePoint)) {
            $timePoint = Carbon::now();
        }

        $this->getSharedLock(function() use (&$series, $timePoint, $interval) {
            $series = $this->shiftBy($timePoint, $interval);
        });

        return $series;
    }

    /**
     * Clean up the series data that are older than quota['max']
     * @transaction-safe
     *
     * @return void
     */
    public function cleanupSeries($timePoint = null, $interval = '1 month')
    {
        if (is_null($timePoint)) {
            $timePoint = Carbon::now();
        }

        $mode = 'r+';
        $this->getExclusiveLock($mode, function($writer) use ($timePoint, $interval) {
            $this->load();

            // rebasing the start
            list($currentStart, $currentValue) = $this->archived;
            if (is_null($currentStart) || $currentStart < $this->quota['start']) {
                $this->archived = [$this->quota['start'], 0];
            } elseif ($currentStart > $this->quota['start']) {
                $this->archived = [$this->quota['start'], $currentValue];
            }

            // strip series coming before the start date
            $offset = null;
            foreach ($this->series as $i => $value) {
                if ($value >= $this->quota['start']) {
                    $offset = $i;
                    break;
                }
            }
            $this->series = array_slice($this->series, $offset);

            // compute the archive as well as reset the series
            $cutOff = $timePoint->copy()->sub(\DateInterval::createFromDateString($interval))->timestamp;
            $offset = null;
            foreach ($this->series as $i => $value) {
                if ($value >= $cutOff) {
                    $offset = $i;
                    break;
                }
            }

            $archived = array_slice($this->series, 0, $offset);
            $newSeries = array_slice($this->series, $offset);

            // retrieve the latest archive data
            list($start, $value) = $this->archived;
            $value += count($archived);
            $archivedStr = sprintf("%s:%s|", $start, $value);

            // write to file
            ftruncate($writer, 0);
            fwrite($writer, $archivedStr . implode(",", $newSeries));
            fflush($writer);
        });
    }

    /**
     * Get exclusive lock, preparing to read/write quota data
     *
     * @return void
     * @deprecated due to performance issue (write the entire series to the file, mod "w")
     */
    public function getExclusiveLock($mode, $callback)
    {
        $reader = fopen($this->file, $mode);
        $start = time();
        while (true) {
            // if timed out
            $this->timeout($start);

            if (flock($reader, LOCK_EX | LOCK_NB)) {  // acquire an exclusive lock
                $this->hasLock = true;

                // execute the callback
                $callback($reader);

                //ftruncate($reader, 0);
                //fwrite($reader, implode(",", $this->series));
                fflush($reader);
                $this->hasLock = false;
                flock($reader, LOCK_UN);    // release the lock
                fclose($reader);
                break;
            }

            // Otherwise, loop and wait to for the lock
        }
    }

    /**
     * Get shared lock, preparing to read quota data
     *
     * @return void
     */
    public function getSharedLock($callback)
    {
        if ($this->hasLock) {
            // execute the callback
            $callback($this);
            return;
        }

        $this->createLogFile();
        $reader = fopen($this->file, "r");
        $start = time();
        while (true) {
            // if timed out
            $this->timeout($start);

            if (flock($reader, LOCK_SH | LOCK_NB)) {  // acquire an exclusive lock
                $this->hasLock = true;
                // retrieve the series data
                $this->load();

                // execute the callback
                $callback($this);

                $this->hasLock = false;
                flock($reader, LOCK_UN);    // release the lock
                fclose($reader);

                break;
            }

            // Otherwise, loop and wait to for the lock
        }
    }

    /**
     * Load time series from file
     *
     * @return array time series
     */
    private function load()
    {
        $series = file_get_contents($this->file);
        preg_match('/(?<archived>^[^\|]{1,24})\|/', $series, $matched);
        if (array_key_exists('archived', $matched)) {
            $this->archived = array_map('intval', explode(':', $matched['archived'])); // [ $start, $value]
        } else {
            $this->archived = [null, 0];
        }

        // @note the method below is deprecated as it is quite slow
        //     return array_map('intval', array_values(array_filter(explode(',', file_get_contents($this->file)))));
        // a faster way is to use json_decode

        $this->series = json_decode('[' . preg_replace('/^(.*\|,*|,+)/', '', $series) . ']');
        return $this->series;
    }

    /**
     * Time out a lock request when it exceeds the QUOTA setting
     *
     * @return void
     */
    private function timeout($start)
    {
        if (time() - $start > self::TIMEOUT) {
            throw new \Exception("Timeout getting lock");
        }
    }

    /**
     * Get the raw series data (for testing only)
     *
     * @return Array series
     */
    public function getRawSeries()
    {
        return $this->load();
    }

    /**
     * Get the raw archived info (for testing only)
     *
     * @return Array series
     */
    public function getRawArchived()
    {
        return $this->archived;
    }

    /**
     * Dump the quota track configuration settings
     *
     * @return Array settings
     */
    public function dump()
    {
        return [
            'file' => $this->file,
            'quota' => $this->quota,
            'limits' => $this->limits,
            'usage' => $this->getUsage(),
        ];
    }
}
