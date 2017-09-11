<?php

/**
 * SendingServerAmazon class.
 *
 * An abstract class for different types of Amazon sending servers
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

class SendingServerAmazon extends SendingServer
{
    const SNS_TOPIC = 'ACELLEHANDLER';
    const SNS_TYPE = 'amazon'; // @TODO

    public $notificationTypes = array('Bounce', 'Complaint');
    public static $snsClient = null;
    public static $sesClient = null;
    public static $isSnsSetup = false;

    /**
     * Initiate a AWS SNS session and return the session object (snsClient).
     *
     * @return mixed
     */
    public function snsClient()
    {
        if (!self::$snsClient) {
            self::$snsClient = \Aws\Sns\SnsClient::factory(array(
                'credentials' => array(
                    'key' => trim($this->aws_access_key_id),
                    'secret' => trim($this->aws_secret_access_key),
                ),
                'region' => $this->aws_region,
                'version' => '2010-03-31'
            ));
        }

        return self::$snsClient;
    }

    /**
     * Initiate a AWS SES session and return the session object (snsClient).
     *
     * @return mixed
     */
    public function sesClient()
    {
        if (!self::$sesClient) {
            self::$sesClient = \Aws\Ses\SesClient::factory(array(
                'credentials' => array(
                    'key' => trim($this->aws_access_key_id),
                    'secret' => trim($this->aws_secret_access_key),
                ),
                'region' => $this->aws_region,
                'version' => '2010-12-01'
            ));
        }

        return self::$sesClient;
    }

    /**
     * Setup AWS SNS for bounce and feedback loop.
     *
     * @return mixed
     */
    public function setupSns($message)
    {
        if (self::$isSnsSetup) {
            return true;
        }

        MailLog::info('Set up Amazon SNS for email delivery tracking');

        $fromEmail = array_keys($message->getFrom())[0];

        $awsIdentity = $fromEmail;
        $verifyByDomain = false;
        try {
            $this->sesClient()->setIdentityFeedbackForwardingEnabled(array(
                'Identity' => $awsIdentity,
                'ForwardingEnabled' => true,
            ));
        } catch (\Exception $e) {
            $verifyByDomain = true;
            MailLog::warning("From Email address {$fromEmail} not verified by Amazon SES, using domain instead");
        }

        if ($verifyByDomain) {
            // Use domain name as Aws Identity
            $awsIdentity = substr(strrchr($fromEmail, '@'), 1); // extract domain from email
            $this->sesClient()->setIdentityFeedbackForwardingEnabled(array(
                'Identity' => $awsIdentity, // extract domain from email
                'ForwardingEnabled' => true,
            ));
        }

        $topicResponse = $this->snsClient()->createTopic(array('Name' => self::SNS_TOPIC));
        $subscribeUrl = StringHelper::joinUrl(Setting::get('url_delivery_handler'), self::SNS_TYPE);

        $subscribeResponse = $this->snsClient()->subscribe(array(
            'TopicArn' => $topicResponse->get('TopicArn'),
            'Protocol' => stripos($subscribeUrl, 'https') === 0 ? 'https' : 'http',
            'Endpoint' => $subscribeUrl,
        ));

        if (stripos($subscribeResponse->get('SubscriptionArn'), 'pending') === false) {
            $this->subscription_arn = $result->get('SubscriptionArn');
        }

        foreach ($this->notificationTypes as $type) {
            $this->sesClient()->setIdentityNotificationTopic(array(
                'Identity' => $awsIdentity,
                'NotificationType' => $type,
                'SnsTopic' => $topicResponse->get('TopicArn'),
            ));
        }

        self::$isSnsSetup = true;
    }
}
