<?php

namespace Acelle\Helpers;

class ImportSubscribersHelper
{   
    /**
    * Get message for status of importing job
    *
    * @return string
    */
    public static function getMessage($job)
    {
        $data = json_decode($job->data);
        
        if($data->status == 'failed') {
            $message = trans('messages.import_failed_message', ['error' => $data->error_message]);
        } else if ($job->isNew() && !$job->isCancelled()) {
            $message = trans('messages.starting');
        } else if ($job->isCancelled()) {
            $message = trans('messages.cancelled');
        } else {
            $message = trans('messages.import_export_statistics_line', [
                'total' => $data->total,
                'processed' => $data->processed,
                'success' => $data->processed,
                'error' => 0,
            ]);;
        }
        
        return $message;
    }
}