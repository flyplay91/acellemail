<?php
/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 6/4/16
 */

namespace ElasticEmail\V2\Requests;

interface RequestInterface
{
    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getHttpClient();
}
