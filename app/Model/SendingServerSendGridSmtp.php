<?php

/**
 * SendingServerSendGridApi class.
 *
 * Abstract class for SendGrid API sending server
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
use Smtpapi\Header;

class SendingServerSendGridSmtp extends SendingServerSendGrid
{
    protected $table = 'sending_servers';

    /**
     * Send the provided message.
     *
     * @return boolean
     * @param message
     */
    // Inherit class to implementation of this method
    public function send($message, $params = array())
    {
        try {
            $this->setupWebhooks();

            $msgId = $message->getHeaders()->get('X-Acelle-Message-Id')->getFieldBody();

            $header = new \Smtpapi\Header();
            $header->setUniqueArgs(array('runtime_message_id' => $msgId));
            $message->getHeaders()->addTextHeader(HEADER::NAME, $header->jsonString());

            if (is_null($this->subAccount)) {
                // use master account
                $username = $this->smtp_username;
                $password = $this->smtp_password;
            } else {
                // use sub account
                $username = $this->subAccount->getSubAccountUsername();
                $password = decrypt($this->subAccount->password);
            }

            $transport = \Swift_SmtpTransport::newInstance($this->host, (int) $this->smtp_port, $this->smtp_protocol)
              ->setUsername($username)
              ->setPassword($password)
            ;

            // Create the Mailer using your created Transport
            $mailer = \Swift_Mailer::newInstance($transport);

            // Actually send
            $sent = $mailer->send($message);

            if ($sent) {
                MailLog::info('Sent!');

                $result = array(
                    'runtime_message_id' => $msgId,
                    'status' => self::DELIVERY_STATUS_SENT,
                );

                if (!is_null($this->subAccount)) {
                    $result['sub_account_id'] = $this->subAccount->id;
                }

                return $result;
            } else {
                throw new \Exception("Unknown SMTP error");
            }
        } catch (\Exception $e) {
            MailLog::warning('Sending failed');
            MailLog::warning($e->getMessage());

            $result = array(
                'status' => self::DELIVERY_STATUS_FAILED,
                'error' => $e->getMessage(),
            );

            if (!is_null($this->subAccount)) {
                $result['sub_account_id'] = $this->subAccount->id;
            }

            return $result;
        }
    }
}
