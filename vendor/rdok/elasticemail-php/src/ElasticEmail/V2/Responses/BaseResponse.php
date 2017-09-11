<?php
/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 6/4/16
 */

namespace ElasticEmail\V2\Responses;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Response as Psr7Response;

abstract class BaseResponse
{
    /**
     * @var Client
     */
    protected $httpClient;
    protected $rawContents;
    private $contents;

    public function __construct(Psr7Response $client)
    {
        $this->httpClient = $client;

        $this->rawContents = $client->getBody()->getContents();
        $this->contents = json_decode($this->rawContents);

        if (!$this->wasSuccessful()) {
            throw new ResponseException($this->getErrorMessage());
        }
    }

    /**
     * The API status response of the requested action.
     *
     * @return bool
     */
    public function wasSuccessful()
    {
        return $this->contents->success;
    }

    /**
     * The API error message response of the requested action.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        if ($this->wasSuccessful()) {
            return null;
        }

        return $this->contents->error;
    }

    /**
     * The API data response.
     *
     * @return bool
     */
    public function getData()
    {
        return $this->rawContents;
    }

    /**
     * Get the HTTP status code.
     *
     * @return Response
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function getTransactionId()
    {
        return $this->contents->data->transactionid;
    }

    public function getMessageId()
    {
        return $this->contents->data->messageid;
    }
}
