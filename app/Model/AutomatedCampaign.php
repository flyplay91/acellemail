<?php

/**
 * Automated Campaign Class
 *
 * Model class for automated campaigns.
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
use Carbon\Carbon;
use Acelle\Model\Campaign;
use Acelle\Jobs\RunAutomatedCampaignJob;
use Acelle\Library\Log as MailLog;
use Acelle\Library\StringHelper;

class AutomatedCampaign extends AbsCampaign
{
    protected $table = 'campaigns';
    protected $trigger = null;

    /**
     * Start the auto campaign
     *
     */
    public function start($trigger = null, $subscribers = null) {
        if (is_null($trigger)) {
            throw new \Exception("Trigger must not be null for Automated Campaign");
        }

        // set the trigger
        $this->trigger = $trigger;

        try {
            MailLog::info('Start auto campaign `'.$this->name.'`, trigger ID: ' . $this->trigger->id);

            // Reset max_execution_time so that command can run for a long time without being terminated
            self::resetMaxExecutionTime();

            // @todo now only run as single process
            $this->run($subscribers);

            MailLog::info('Finish auto campaign `'.$this->name.'`');
        } catch (\Exception $ex) {
            // Set ERROR status
            $this->error($ex->getMessage());
            MailLog::error('Auto campaign failed. '.$ex->getMessage());
        }
    }

    public function run($subscribers = null)
    {
        try {
            // clean up the tracker to prevent the log file from growing very big
            $this->customer->cleanupQuotaTracker();

            $i = 0;
            if (is_null($subscribers)) {
                $subscribers = $this->trigger->autoEvent->automation->subscribers()->get();
            }
            foreach ($subscribers as $subscriber) {
                if ($this->customer->overQuota()) {
                    throw new \Exception("Customer (UID: {$this->customer->uid}) has reached sending quota");
                }

                $i += 1;
                MailLog::info("Sending to subscriber `{$subscriber->email}` ({$i}/".sizeof($subscribers).')');

                // Pick up an available sending server
                // Throw exception in case no server available
                $server = $this->pickSendingServer();

                list($message, $msgId) = $this->prepareEmail($subscriber, $server);

                $response = $server->send($message);

                // track the auto trigger id
                $response['auto_trigger_id'] = $this->trigger->id;

                // record to tracking log
                $this->trackMessage($response, $subscriber, $server, $msgId);
            }
        } catch (\Exception $e) {
            MailLog::error($e->getMessage());
            $this->error($e->getMessage());
        } finally {
            // reset server pools: just in case DeliveryServerAmazonSesWebApi
            // --> setup SNS requires using from_email of the corresponding campaign
            // but SNS is only made once when the server is initiated
            //     SendingServer::resetServerPools();
        }
    }

    /**
     * Pick up a delivery server for the campaign.
     *
     * @return mixed
     */
    public function pickSendingServer()
    {
        return $this->trigger->autoEvent->automation->defaultMailList->pickSendingServer();
    }

    /**
     * Transform Tags
     * Transform tags to actual values before sending.
     */
    public function tagMessage($message, $subscriber, $msgId, $server)
    {
        if (!is_null($server) && $server->isElasticEmailServer()) {
            $message = $server->addUnsubscribeUrl($message);
        }

        $list = $this->trigger->autoEvent->automation->defaultMailList;

        $tags = array(
            'SUBSCRIBER_EMAIL' => $subscriber->email,
            'CAMPAIGN_NAME' => $this->name,
            'CAMPAIGN_UID' => $this->uid,
            'CAMPAIGN_SUBJECT' => $this->subject,
            'CAMPAIGN_FROM_EMAIL' => $this->from_email,
            'CAMPAIGN_FROM_NAME' => $this->from_name,
            'CAMPAIGN_REPLY_TO' => $this->reply_to,
            'SUBSCRIBER_UID' => $subscriber->uid,
            'CURRENT_YEAR' => date('Y'),
            'CURRENT_MONTH' => date('m'),
            'CURRENT_DAY' => date('d'),
            'UNSUBSCRIBE_URL' => str_replace('MESSAGE_ID', StringHelper::base64UrlEncode($msgId), Setting::get('url_unsubscribe')),
            'CONTACT_NAME' => $list->contact->company,
            'CONTACT_COUNTRY' => $list->contact->country->name,
            'CONTACT_STATE' => $list->contact->state,
            'CONTACT_CITY' => $list->contact->city,
            'CONTACT_ADDRESS_1' => $list->contact->address_1,
            'CONTACT_ADDRESS_2' => $list->contact->address_2,
            'CONTACT_PHONE' => $list->contact->phone,
            'CONTACT_URL' => $list->contact->url,
            'CONTACT_EMAIL' => $list->contact->email,
            'LIST_NAME' => $list->name,
            'LIST_SUBJECT' => $list->default_subject,
            'LIST_FROM_NAME' => $list->from_name,
            'LIST_FROM_EMAIL' => $list->from_email,
            'WEB_VIEW_URL' => str_replace('MESSAGE_ID', StringHelper::base64UrlEncode($msgId), Setting::get('url_web_view')),
        );

        // UPDATE_PROFILE_URL
        if(!$this->isStdClassSubscriber($subscriber)) {
            // in case of actually sending campaign
            $tags['UPDATE_PROFILE_URL'] = str_replace('LIST_UID', $this->trigger->autoEvent->automation->defaultMailList->uid,
                str_replace('SUBSCRIBER_UID', $subscriber->uid,
                str_replace('SECURE_CODE', $subscriber->getSecurityToken('update-profile'), Setting::get('url_update_profile'))));
        }

        // Update tags layout
        foreach ($tags as $tag => $value) {
            $message = str_replace('{'.$tag.'}', $value, $message);
        }

        if (!$this->isStdClassSubscriber($subscriber)) {
            // in case of actually sending campaign
            foreach ($this->trigger->autoEvent->automation->defaultMailList->fields as $field) {
                $message = str_replace('{SUBSCRIBER_'.$field->tag.'}', $subscriber->getValueByField($field), $message);
            }
        } else {
            // in case of sending test email
            // @todo how to manage such tags?
            $message = str_replace('{SUBSCRIBER_EMAIL}', $subscriber->email, $message);
        }

        return $message;
    }

    /**
     * Queue campaign for sending
     *
     */
    public function queue($subscribers = null, $trigger = null, $delay = 0)
    {
        // @note only set the trigger when start()
        // since campaign can be started without queue()
        MailLog::info(sprintf('Automated campaign `%s` (ID: %s) queued, trigger ID: %s', $this->name, $this->id, $trigger->id ));
        $job = ((new RunAutomatedCampaignJob($this, $trigger, $subscribers))->delay($delay));
        dispatch($job);
    }
}
