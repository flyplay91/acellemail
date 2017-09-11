<?php

/**
 * SendingServerElasticEmail class.
 *
 * Abstract class for Mailjet sending server
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

use Acelle\Library\Log as MailLog;
use Acelle\Library\StringHelper;
use Acelle\Model\TrackingLog;
use Acelle\Model\BounceLog;
use Acelle\Model\FeedbackLog;

class SendingServerElasticEmail extends SendingServer
{
    const WEBHOOK = 'elasticemail';
    const API_ENDPOINT = 'https://api.elasticemail.com/v2';

    protected $table = 'sending_servers';
    public static $client = null;
    public static $isWebhookSetup = false;
    public static $isCustomHeadersEnabled = false;

    /**
     * Get authenticated to Mailgun and return the session object.
     *
     * @return mixed
     */
    public function client()
    {
        if (!self::$client) {
            self::$client = new \ElasticEmail\ElasticEmailV2($this->api_key);
        }

        return self::$client;
    }

    /**
     * Process unsubscribe URL
     * See documentation at: https://elasticemail.com/support/user-interface/unsubscribe/#Unsubscribe-Link
     * @return string message
     */
    public function addUnsubscribeUrl($message)
    {
        return str_replace('{UNSUBSCRIBE_URL}', '{unsubscribe:{UNSUBSCRIBE_URL}}', $message);
    }

    /**
     * Handle notification from ElasticEmail
     * Handle Bounce/Feedback/Error
     *
     * @return mixed
     */
    public static function handleNotification($params) {
        // bounce
        if (strcasecmp($params['status'], 'Error') == 0) {
            $bounceLog = new BounceLog();

            // use Elastic Email transaction id as runtime-message-id
            $bounceLog->runtime_message_id = $params['transaction'];
            $trackingLog = TrackingLog::where('runtime_message_id', $bounceLog->runtime_message_id)->first();
            if ($trackingLog) {
                $bounceLog->message_id = $trackingLog->message_id;
            }

            $bounceLog->bounce_type = BounceLog::HARD;
            $bounceLog->raw = json_encode($params);
            $bounceLog->save();
            MailLog::info('Bounce recorded for message '.$bounceLog->runtime_message_id);

            // add subscriber's email to blacklist
            $subscriber = $bounceLog->findSubscriberByRuntimeMessageId();
            if ($subscriber) {
                $subscriber->sendToBlacklist($bounceLog->raw);
                MailLog::info('Email added to blacklist');
            } else {
                MailLog::warning('Cannot find associated tracking log for ElasticEmail message '.$bounceLog->runtime_message_id);
            }
        } else if (strcasecmp($params['status'], 'AbuseReport') == 0) {
            $feedbackLog = new FeedbackLog();

            // use Elastic Email transaction id as runtime-message-id
            $feedbackLog->runtime_message_id = $params['transaction'];

            // retrieve the associated tracking log in Acelle
            $trackingLog = TrackingLog::where('runtime_message_id', $feedbackLog->runtime_message_id)->first();
            if ($trackingLog) {
                $feedbackLog->message_id = $trackingLog->message_id;
            }

            // ElasticEmail only notifies in case of SPAM reported
            $feedbackLog->feedback_type = 'spam';
            $feedbackLog->raw_feedback_content = json_encode($params);
            $feedbackLog->save();
            MailLog::info('Feedback recorded for message '.$feedbackLog->runtime_message_id);

            // update the mail list, subscriber to be marked as 'spam-reported'
            // @todo: the following lines of code should be wrapped up in one single method: $feedbackLog->markSubscriberAsSpamReported();
            $subscriber = $feedbackLog->findSubscriberByRuntimeMessageId();
            if ($subscriber) {
                $subscriber->markAsSpamReported();
                MailLog::info('Subscriber marked as spam-reported');
            } else {
                MailLog::warning('Cannot find associated tracking log for ElasticEmail message '.$feedbackLog->runtime_message_id);
            }
        }
    }

    /**
     * Enable custom headers.
     * By default, customers headers are suppressed by Elastic Email
     *
     * @return mixed
     */
    public function enableCustomHeaders()
    {
        if (self::$isCustomHeadersEnabled) {
            return true;
        }

        try {
            $response = file_get_contents(self::API_ENDPOINT."/account/updatehttpnotification?apikey=".$this->api_key."&allowCustomHeaders=true");
            $responseJson = json_decode($response);


            if ($responseJson->success == true) {
                MailLog::info("Custom headers enabled");
                self::$isCustomHeadersEnabled = true;
            } else {
                throw new Exception("Cannot enable customer headers: ".$response);
            }
        } catch (\Exception $e) {
            MailLog::warning($e->getMessage());
        }
    }

    /**
     * Setup webhooks for processing bounce and feedback loop.
     *
     * @return mixed
     */
    public function setupWebhooks()
    {
        if (self::$isWebhookSetup) {
            return true;
        }

        try {
            $subscribeUrl = StringHelper::joinUrl(Setting::get('url_delivery_handler'), self::WEBHOOK);

            $response = file_get_contents(self::API_ENDPOINT."/account/updatehttpnotification?apikey=".$this->api_key."&url=".$subscribeUrl."&settings={sent:false,opened:false,clicked:false,unsubscribed:false,complaints:true,error:true}");
            if ($response == '{"success":true}') {
                MailLog::info("webhook set!");
                self::$isWebhookSetup = true;
            } else {
                throw new Exception("Cannot setup webhook. Response from server: ".$response);
            }
        } catch (\Exception $e) {
            MailLog::warning($e->getMessage());
        }
    }

    /**
     * Unescape the HTML attributes escaped by DOMDocument (inline CSS maker)
     * For example: conver <a href="%7Bunsubscribe:...%7D" to <a href="{unsubscribe:...}"
     *
     * @return mixed
     */
    public function unescapeUnsubscribeUrl($message)
    {
        preg_match_all('/(?<matched>\%7Bunsubscribe:.*?\%7D)/', $message, $result);
        foreach ($result['matched'] as $occurrence) {
            $message = str_replace($occurrence, urldecode($occurrence), $message);
        }

        return $message;
    }

    /**
     * Delivery message
     *
     * @return mixed
     */
    public function sendElasticEmailV2($message) {
        // @todo: what if there are more than 2 parts?
        $html = null;
        $plain = null;
        foreach($message->getChildren() as $part) {
            $contentType = $part->getContentType();
            if ($contentType == 'text/html') {
                $html = $part->getBody();
            } else if ($contentType == 'text/plain') {
                $plain = $part->getBody();
            }
        }

        if (!is_null($html)) {
            $html = $this->unescapeUnsubscribeUrl($html);
        }

        // @todo: custom headers not correctly supported by Elastic Email API v2
        $fromEmail = array_keys($message->getFrom())[0];
        $fromName = (is_null($message->getFrom())) ? null : array_values($message->getFrom())[0];
        $toEmail = array_keys($message->getTo())[0];
        $replyToEmail = (is_null($message->getReplyTo())) ? null : array_keys($message->getReplyTo())[0];

        $result = $this->client()->email()->send([
            'to' => $toEmail,
            'replyTo' => $replyToEmail,
            'subject' => $message->getSubject(),
            'from' => $fromEmail,
            'fromName' => $fromName,
            'bodyHtml' => $html,
            'bodyText' => $plain,
            'charset' => 'utf-8',
        ]);

        $jsonResponse = json_decode($result->getData());

        // Use transactionid returned from ElasticEmail as runtime_message_id
        return $jsonResponse->data->transactionid;
    }
}
