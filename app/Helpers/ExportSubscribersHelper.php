<?php

namespace Acelle\Helpers;

class ExportSubscribersHelper
{   
    /**
    * Get message for status of importing job
    *
    * @return string
    */
    public static function getMessage($job)
    {
        $data = json_decode($job->data);
        
        if($job->isCancelled()) {
            return trans('messages.cancelled');
        }
        
        return $data->message;
    }
}