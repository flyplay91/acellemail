<?php
/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 6/5/16
 */

namespace ElasticEmail;

use ElasticEmail\Exceptions\ElasticEmailV2Exception;
use ElasticEmail\V2\Requests\Email\EmailRequest;

class ElasticEmailV2
{
    const API_KEY = 'apikey';
    protected $baseUrlV2 = 'https://api.elasticemail.com/v2/';
    private $apiKey;
    private $emailRequest;

    public function __construct($apiKey)
    {
        if (is_null($apiKey)) {
            throw new ElasticEmailV2Exception('Missing API key.');
        }

        $this->apiKey = $apiKey;
    }

    /**
     * @return EmailRequest
     */
    public function email()
    {
        if (is_null($this->getEmailRequest())) {
            $this->setEmailRequest(new EmailRequest($this->baseUrlV2, [self::API_KEY => $this->apiKey]));
        }

        return $this->getEmailRequest();
    }

    /**
     * @return mixed
     */
    public function getEmailRequest()
    {
        return $this->emailRequest;
    }

    /**
     * @param mixed $emailRequest
     */
    public function setEmailRequest($emailRequest)
    {
        $this->emailRequest = $emailRequest;
    }
}
