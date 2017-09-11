<?php

namespace Acelle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class SystemJobPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function delete(\Acelle\Model\User $user, \Acelle\Model\SystemJob $item)
    {
        if($item->name == 'Acelle\Jobs\ImportSubscribersJob' || $item->name == 'Acelle\Jobs\ExportSubscribersJob') {
            $data = json_decode($item->data);
            $list = \Acelle\Model\MailList::findByUid($data->mail_list_uid);
            return $list->customer_id == $user->customer->id && !$item->isRunning();
        }

        return false;
    }

    public function downloadImportLog(\Acelle\Model\User $user, \Acelle\Model\SystemJob $item)
    {
        $data = json_decode($item->data);
        $list = \Acelle\Model\MailList::findByUid($data->mail_list_uid);
        return $list->customer_id == $user->customer->id &&
            $item->name == 'Acelle\Jobs\ImportSubscribersJob' &&
            $data->status == 'done';
    }

    public function downloadExportCsv(\Acelle\Model\User $user, \Acelle\Model\SystemJob $item)
    {
        $data = json_decode($item->data);
        $list = \Acelle\Model\MailList::findByUid($data->mail_list_uid);
        return $list->customer_id == $user->customer->id &&
            $item->name == 'Acelle\Jobs\ExportSubscribersJob' &&
            $data->status == 'done';
    }

    public function cancel(\Acelle\Model\User $user, \Acelle\Model\SystemJob $item)
    {
        if($item->name == 'Acelle\Jobs\ImportSubscribersJob' || $item->name == 'Acelle\Jobs\ExportSubscribersJob') {
            $data = json_decode($item->data);
            $list = \Acelle\Model\MailList::findByUid($data->mail_list_uid);
            return $list->customer_id == $user->customer->id &&
                $item->isRunning();
        }

        return false;
    }
}
