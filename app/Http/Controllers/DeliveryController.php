<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Library\Log as MailLog;
use Acelle\Library\StringHelper;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use GuzzleHttp\Client;
use Acelle\Model\TrackingLog;
use Acelle\Model\BounceLog;
use Acelle\Model\SendingServer;
use Acelle\Model\SendingServerMailgun;
use Acelle\Model\SendingServerSendGrid;
use Acelle\Model\SendingServerElasticEmail;
use Acelle\Model\SendingServerSparkPost;
use Acelle\Model\FeedbackLog;

class DeliveryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', [
            'except' => [
                'notify',
            ],
        ]);
    }


    /**
     * Campaign notification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function notify(Request $request)
    {
        // Make sure the request is POST
        // ElasticEmail send notification via GET
        // if (!$request->isMethod('post')) {
        //     return;
        // }

        $type = $request->stype;

        echo $type;

        if ($type == 'amazon') { // @TODO hard-coded here, seeking for a solution
            $this->handleAws();
        } elseif ($type == SendingServerMailgun::WEBHOOK) {
            $this->handleMailgun();
        } elseif ($type == SendingServerSendGrid::WEBHOOK) {
            $this->handleSendGrid();
        } elseif ($type == SendingServerElasticEmail::WEBHOOK) {
            MailLog::configure(storage_path().'/logs/handler-elasticemail.log');
            SendingServerElasticEmail::handleNotification($_GET);
        } elseif ($type == SendingServerSparkPost::WEBHOOK) {
            MailLog::configure(storage_path().'/logs/handler-sparkpost.log');
            return SendingServerSparkPost::handleNotification($_GET);
        } else {
            return;
        }
    }

    /**
     * Handle SendGrid Event Notification
     *
     * @param SendGrid POST
     */
    private function handleSendGrid()
    {
        MailLog::configure(storage_path().'/logs/handler-sendgrid.log');
        $messages = json_decode(file_get_contents('php://input'), true);
        MailLog::info(file_get_contents('php://input'));

        foreach($messages as $message) {
            switch ($message['event']) {
                case 'dropped':
                case 'bounce':
                    $bounceLog = new BounceLog();

                    // runtime-message_id is set by SendGrid SMTP API
                    // in case of SendGrid Web API, use sg_message_id instead
                    if (array_key_exists("runtime_message_id", $message)) {
                        $bounceLog->runtime_message_id = $message["runtime_message_id"];
                    }

                    // retrieve the associated tracking log in Acelle
                    $trackingLog = TrackingLog::where('runtime_message_id', $bounceLog->runtime_message_id)->first();
                    if ($trackingLog) {
                        $bounceLog->message_id = $trackingLog->message_id;
                    }

                    // SendGrid only notifies in case of HARD bounce
                    $bounceLog->bounce_type = BounceLog::HARD;
                    $bounceLog->raw = json_encode($message);
                    $bounceLog->save();
                    MailLog::info('Bounce recorded for message '.$bounceLog->runtime_message_id);

                    // add subscriber's email to blacklist
                    $subscriber = $bounceLog->findSubscriberByRuntimeMessageId();
                    if ($subscriber) {
                        $subscriber->sendToBlacklist($bounceLog->raw);
                        MailLog::info('Email added to blacklist');
                    } else {
                        MailLog::warning('Cannot find associated tracking log for SendGrid message '.$bounceLog->runtime_message_id);
                    }
                    break;
                case 'spamreport':
                    $feedbackLog = new FeedbackLog();

                    // runtime-message_id is set by SendGrid SMTP API
                    // in case of SendGrid Web API, use sg_message_id instead
                    if (array_key_exists("runtime_message_id", $message)) {
                        $feedbackLog->runtime_message_id = $message["runtime_message_id"];
                    }

                    // retrieve the associated tracking log in Acelle
                    $trackingLog = TrackingLog::where('runtime_message_id', $bounceLog->runtime_message_id)->first();
                    if ($trackingLog) {
                        $feedbackLog->message_id = $trackingLog->message_id;
                    }

                    // SendGrid only notifies in case of SPAM reported
                    $feedbackLog->feedback_type = 'spam';
                    $feedbackLog->raw_feedback_content = json_encode($message);
                    $feedbackLog->save();
                    MailLog::info('Feedback recorded for message '.$feedbackLog->runtime_message_id);

                    // update the mail list, subscriber to be marked as 'spam-reported'
                    $subscriber = $feedbackLog->findSubscriberByRuntimeMessageId();
                    if ($subscriber) {
                        $subscriber->markAsSpamReported();
                        MailLog::info('Subscriber marked as spam-reported');
                    } else {
                        MailLog::warning('Cannot find associated tracking log for SendGrid message '.$feedbackLog->runtime_message_id);
                    }
                    break;
                default:
                    // nothing
            }
        }

        header('X-PHP-Response-Code: 200', true, 200);
    }

    private function handleMailgun()
    {
        MailLog::configure(storage_path().'/logs/handler-mailgun.log');

        // @TODO: POST request not verified because we cannot retrive sending server information
        // The complete check should be
        //    if (isset($_POST['timestamp']) && isset($_POST['token']) && isset($_POST['signature']) && hash_hmac('sha256', $_POST['timestamp'].$_POST['token'], $sendingServer->api_key) === $_POST['signature']) {
        if (isset($_POST['timestamp']) && isset($_POST['token']) && isset($_POST['signature'])) {
            if ($_POST['event'] == 'complained') {
                $feedbackLog = new FeedbackLog();
                $feedbackLog->runtime_message_id = StringHelper::cleanupMessageId($_POST['Message-Id']);
                // For Mailgun, runtime_message_id EQUIV. message_id
                $feedbackLog->message_id = $feedbackLog->runtime_message_id;
                $feedbackLog->feedback_type = 'spam';
                $feedbackLog->raw_feedback_content = '';
                $feedbackLog->save();
                MailLog::info('Feedback recorded for message '.$feedbackLog->runtime_message_id);
            } elseif ($_POST['event'] == 'bounced') {
                $bounceLog = new BounceLog();
                $bounceLog->runtime_message_id = StringHelper::cleanupMessageId($_POST['Message-Id']);
                // For Mailgun, runtime_message_id EQUIV. message_id
                $bounceLog->message_id = $bounceLog->runtime_message_id;
                $bounceLog->bounce_type = BounceLog::HARD;
                $bounceLog->raw = $_POST['error'];
                $bounceLog->save();
                MailLog::info('Bounce recorded for message '.$bounceLog->runtime_message_id);
                MailLog::info('Adding email to blacklist');
                $bounceLog->findSubscriberByRuntimeMessageId()->sendToBlacklist($bounceLog->raw);
            }
        }
        header('X-PHP-Response-Code: 200', true, 200);
    }

    private function handleAws()
    {
        MailLog::configure(storage_path().'/logs/handler-aws.log');

        try {
            MailLog::info('validating message...');
            // Create a message from the post data and validate its signature
            $message = Message::fromRawPostData();
            $validator = new MessageValidator();
            $validator->validate($message);
            MailLog::info('message validated!');
        } catch (\Exception $e) {
            // Pretend we're not here if the message is invalid
            MailLog::warning('not an Amazon push');

            return;
        }

        if ($message['Type'] === 'SubscriptionConfirmation') {
            MailLog::info('subscription received');
            // Send a request to the SubscribeURL to complete subscription
            (new Client())->get($message['SubscribeURL']);

            MailLog::info('subscription confirmed');

            return;
        }

        if ($message['Type'] != 'Notification') {
            MailLog::info('not notification');

            return;
        }

        $responseMessage = json_decode($message['Message'], true);

        if ($responseMessage['notificationType'] == 'AmazonSnsSubscriptionSucceeded') {
            MailLog::info('subscription confirmed by AWS');

            return;
        }

        /*
        sleep(5);
        $trackingLog = TrackingLog::where("message_id", $responseMessage['mail']['messageId'])->first() ;
        if (empty($trackingLog)) {
            MailLog::error('message_id not found');
            return;
        }
        */

        if ($responseMessage['notificationType'] == 'Bounce') {
            $bounce = $responseMessage['bounce'];

            $bounceLog = new BounceLog();
            $bounceLog->runtime_message_id = $responseMessage['mail']['messageId'];
            $trackingLog = TrackingLog::where('runtime_message_id', $bounceLog->runtime_message_id)->first();
            $bounceLog->message_id = $trackingLog->message_id;
            $bounceLog->bounce_type = $bounce['bounceType']; // !== 'Permanent' ? BounceLog::SOFT : BounceLog::HARD;
            $bounceLog->raw = $message['Message'];
            $bounceLog->save();
            MailLog::info('Bounce recorded for message '.$bounceLog->runtime_message_id);

            if ($bounce['bounceType'] === 'Permanent') {
                MailLog::info('Adding email to blacklist');
                $bounceLog->findSubscriberByRuntimeMessageId()->sendToBlacklist($bounceLog->raw);
            }
        }

        if ($responseMessage['notificationType'] == 'Complaint') {
            $feedback = $responseMessage['complaint'];

            $feedbackLog = new FeedbackLog();
            $feedbackLog->runtime_message_id = $responseMessage['mail']['messageId'];
            $feedbackLog->feedback_type = $feedback['complaintFeedbackType'];
            $feedbackLog->raw_feedback_content = $message['Message'];
            $feedbackLog->save();
            MailLog::info('Feedback recorded for message '.$feedbackLog->runtime_message_id);
            try {
                MailLog::info('Adding email to abuse list');
                $feedbackLog->findSubscriberByRuntimeMessageId()->markAsSpamReported();
            } catch (\Exception $e) {
                MailLog::warning('Cannot mark subscriber as Abuse-Reported. ' . $e->getMessage());
            }
        }
    }
}
