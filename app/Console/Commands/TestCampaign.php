<?php

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;
use Acelle\Library\Log;
use Acelle\Library\QuotaTrackerStd;
use Acelle\Library\QuotaTrackerRedis;
use Acelle\Library\StringHelper;
use Acelle\Library\QuotaTracker;
use Acelle\Model\Campaign;
use Acelle\Model\User;
use Acelle\Model\MailList;
use Acelle\Model\Subscriber;
use Acelle\Model\TrackingLog;
use Acelle\Model\AutoEvent;
use Acelle\Model\SendingServer;
use Acelle\Model\AutoTrigger;
use Acelle\Model\SendingServerElasticEmailApi;
use Acelle\Model\SendingServerElasticEmail;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

class TestCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->testImap();
    }

    public function testSmtp() {
        $transport = \Swift_SmtpTransport::newInstance('smtp.elasticemail.com', 2525, 'tls')
          ->setUsername('')
          ->setPassword('')
          ;

        // Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($transport);

        // Create a message
        $message = \Swift_Message::newInstance('Wonderful Subject')
          ->setFrom(array('' => 'Asish'))
          ->setTo(array('' => 'Louis'))
          ->setBody('Here is the message itself')
          ;

        // Send the message
        $result = $mailer->send($message);

        var_dump($result);
    }

    public function testImap() {
        // Connect to IMAP server
        $imapPath = "{mail.example.com:993/imap/tls}INBOX";

        // try to connect
        $inbox = imap_open($imapPath, 'user@example.com', 'password');

        // search and get unseen emails, function will return email ids
        $emails = imap_search($inbox, 'UNSEEN');

        if (!empty($emails)) {
            foreach ($emails as $message) {
                var_dump($message);
            }
        }

        // colse the connection
        imap_expunge($inbox);
        imap_close($inbox);
    }
}
