<?php
/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 6/3/16
 */

namespace ElasticEmail\V2\Requests\Email;

use ElasticEmail\V2\Requests\BaseRequest;
use ElasticEmail\V2\Requests\RequestInterface;
use ElasticEmail\V2\Responses\Email\EmailResponse;

class EmailRequest extends BaseRequest implements RequestInterface
{
    /**
     * @param array $emailData to, subject, from keys required.
     * @return EmailResponse
     */
    public function send(array $emailData)
    {
        $this->handlerRequestValidation($emailData);

        $guzzleResponse = $this->getHttpClient()->request('POST', 'email/send', [
            'form_params' => array_merge($this->config, $emailData),
        ]);

        return new EmailResponse($guzzleResponse);
    }

    private function handlerRequestValidation($emailData)
    {
        if (!array_key_exists('to', $emailData)) {
            throw new RequestException("At least one recipient must be specified. Array key: 'to'");
        }

        if (!filter_var($emailData['to'], FILTER_VALIDATE_EMAIL)) {
            throw new RequestException('Invalid recipient email.');
        }

        if (!array_key_exists('subject', $emailData)) {
            throw new RequestException('Subject field must be specified.');
        }

        if (!array_key_exists('from', $emailData)) {
            throw new RequestException('Invalid FROM email address.');
        }

        return true;
    }
}
