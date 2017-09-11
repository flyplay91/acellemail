<?php

/**
 * DeliveryHandler class.
 *
 * Abstract class for different types of delivery handlers
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
use Acelle\Library\StringHelper;
use Acelle\Library\Log as MailLog;

class DeliveryHandler extends Model
{
    /**
     * Test the handler connection
     *
     * @return mixed
     */
    public function test()
    {
        // Connect to IMAP server
        $imapPath = "{{$this->host}:{$this->port}/{$this->protocol}/{$this->encryption}}INBOX";

        // try to connect
        $inbox = @imap_open($imapPath, $this->username, $this->password);

        // try again with ssl/novalidate-cert
        if ($inbox == false) {
            MailLog::info($this->encryption);
            if (strcasecmp ( $this->encryption , 'ssl') == 0) {
                MailLog::warning('Try using ssl/novalidate-cert instead');
                $imapPath = "{{$this->host}:{$this->port}/{$this->protocol}/ssl/novalidate-cert}INBOX";
                $inbox = @imap_open($imapPath, $this->username, $this->password);
            }
        }

        // if failed
        if ($inbox == false) {
            throw new \Exception("Cannot connect to the server: $imapPath");
        }

        // search and get unseen emails, function will return email ids
        $emails = imap_search($inbox, 'UNSEEN');

        // colse the connection
        imap_expunge($inbox);
        imap_close($inbox);

        return true;
    }

    /**
     * Start the handling processes.
     *
     * @return mixed
     */
    public function start()
    {
        try {
            // Connect to IMAP server
            $imapPath = "{{$this->host}:{$this->port}/{$this->protocol}/{$this->encryption}}INBOX";

            // try to connect
            $inbox = @imap_open($imapPath, $this->username, $this->password);

            // try again with ssl/novalidate-cert
            if ($inbox == false) {
                MailLog::info($this->encryption);
                if (strcasecmp ( $this->encryption , 'ssl') == 0) {
                    MailLog::warning('Try using ssl/novalidate-cert instead');
                    $imapPath = "{{$this->host}:{$this->port}/{$this->protocol}/ssl/novalidate-cert}INBOX";
                    $inbox = @imap_open($imapPath, $this->username, $this->password);
                }
            }

            // if failed
            if ($inbox == false) {
                throw new \Exception("Cannot connect to the server: $imapPath");
            }

            // search and get unseen emails, function will return email ids
            $emails = imap_search($inbox, 'UNSEEN');

            if (!empty($emails)) {
                foreach ($emails as $message) {
                    $this->processMessage($inbox, $message);
                }
            }

            // colse the connection
            imap_expunge($inbox);
            imap_close($inbox);
        } catch (\Exception $e) {
            // suppress the IMAP error
            // see http://stackoverflow.com/questions/5422405/cant-silence-imap-open-error-notices-in-php
            imap_errors();
            imap_alerts();
            MailLog::error('Cannot connect to handler '.$e->getMessage());
        }
    }

    /**
     * Extract message ID from email .
     *
     * @return string
     */
    public function getMessageId($message)
    {
        preg_match('/(?<=X-Acelle-Message-Id:)\s{0,1}<{0,1}(?<id>[a-zA-Z0-9\.]+[a-zA-Z0-9]+@[a-zA-Z0-9\.\-]+[a-zA-Z0-9]+)/', $message, $matched);
        if (array_key_exists('id', $matched)) {
            return StringHelper::cleanupMessageId($matched['id']);
        }

        // more tolerant matching (case-insensitive, no need for Acelle prefix, etc.)
        preg_match('/(?<=Message-Id:)\s{0,1}<{0,1}(?<id>[a-zA-Z0-9\.]+[a-zA-Z0-9]+@[a-zA-Z0-9\.\-]+[a-zA-Z0-9]+)/i', $message, $matched);
        if (array_key_exists('id', $matched)) {
            return StringHelper::cleanupMessageId($matched['id']);
        }

        return null;
    }

    /**
     * Process bounced / feedback loop messages.
     *
     * @return mixed
     */
    public function processMessage($mbox, $message)
    {
        // for overwriting
    }
}
