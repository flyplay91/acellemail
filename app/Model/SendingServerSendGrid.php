<?php

/**
 * SendingServerSendGrid class.
 *
 * Abstract class for SendGrid sending servers
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
use SendGrid\Mail;
use SendGrid\Email;
use SendGrid\Content;
use Acelle\Model\Campaign;

class SendingServerSendGrid extends SendingServer
{
    const WEBHOOK = 'sendgrid';

    protected $table = 'sending_servers';
    public static $client = null;
    public static $isWebhookSetup = false;

    /**
     * Get authenticated to Mailgun and return the session object.
     *
     * @return mixed
     */
    public function client()
    {
        if (!self::$client) {
            if (is_null($this->subAccount)) {
                MailLog::info("Using master account");
                self::$client = new \SendGrid($this->api_key);
            } else {
                MailLog::info("Using subaccount {$this->subAccount->getSubAccountUsername()}");
                self::$client = new \SendGrid($this->subAccount->api_key);
            }

        }

        return self::$client;
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
            MailLog::info('Setting up SendGrid webhooks');
            $subscribeUrl = StringHelper::joinUrl(Setting::get('url_delivery_handler'), self::WEBHOOK);
            $request_body = json_decode('{
                "bounce": true,
                "click": false,
                "deferred": false,
                "delivered": false,
                "dropped": true,
                "enabled": true,
                "group_resubscribe": false,
                "group_unsubscribe": false,
                "open": false,
                "processed": false,
                "spam_report": true,
                "unsubscribe": false,
                "url": "'.$subscribeUrl.'"
                }'
            );
            $response = $this->client()->client->user()->webhooks()->event()->settings()->patch($request_body);

            if($response->_status_code == '200') {
                MailLog::info('Webhooks successfully set!');
            } else {
                throw new \Exception("Cannot setup SendGrid webhooks");
            }

            self::$isWebhookSetup = true;
        } catch (\Exception $e) {
            MailLog::warning($e->getMessage());
        }
    }

    /**
     * Get Message Id
     * Extract the message id from SendGrid response
     *
     * @return String
     */
    public function getMessageId($headers) {
        preg_match('/(?<=X-Message-Id: ).*/', $headers, $matches);
        if (isset($matches[0])) {
            return $matches[0];
        } else {
            return NULL;
        }
    }

    /**
     * Prepare the email object for sending
     *
     * @return mixed
     */
    public function prepareEmail($message)
    {
        $fromEmail = array_keys($message->getFrom())[0];
        $fromName = (is_null($message->getFrom())) ? null : array_values($message->getFrom())[0];
        $toEmail = array_keys($message->getTo())[0];
        $toName = (is_null($message->getTo())) ? null : array_values($message->getTo())[0];
        $replyToEmail = (is_null($message->getReplyTo())) ? null : array_keys($message->getReplyTo())[0];

        // Following RFC 1341, section 7.2, if either text/html or text/plain are to be sent in your email: text/plain needs to be first
        // So, use array_shift instead of array_pop
        $parts = array_map(function ($part) {
            return new Content($part->getContentType(), $part->getBody());
        }, $message->getChildren());

        $mail = new Mail(
            new Email($fromName, $fromEmail),
            $message->getSubject(),
            new Email($toName, $toEmail),
            array_shift($parts)
        );

        // set Reply-To header
        $mail->setReplyTo(['email' => $replyToEmail]);

        foreach($parts as $part) {
            $mail->addContent($part);
        }

        $preserved = [
            "Content-Transfer-Encoding",
            "Content-Type",
            "MIME-Version",
            "Date",
            "Message-ID",
            "From",
            "Subject",
            "To",
            "Reply-To",
            "Subject",
            "From"
        ];

        foreach($message->getHeaders()->getAll() as $header) {
            if (!in_array($header->getFieldName(), $preserved)) {
                $mail->addHeader($header->getFieldName(), $header->getFieldBody());
            }
        }

        // to track bounce/feedback notification
        $mail->addCustomArg("runtime_message_id", $message->getHeaders()->get('X-Acelle-Message-Id')->getFieldBody());

        return $mail;
    }
}
