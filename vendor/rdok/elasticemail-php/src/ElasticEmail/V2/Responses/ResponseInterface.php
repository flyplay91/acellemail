<?php
/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 6/4/16
 */

namespace ElasticEmail\V2\Responses;

interface ResponseInterface
{
    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getHttpClient();

    /**
     * The API status response of the requested action.
     *
     * @return bool
     */
    public function wasSuccessful();

    /**
     * The API error message response of the requested action.
     *
     * @return bool
     */
    public function getErrorMessage();

    /**
     * The API data response.
     *
     * @return array
     */
    public function getData();
}
