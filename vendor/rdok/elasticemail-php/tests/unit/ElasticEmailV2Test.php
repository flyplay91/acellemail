<?php
use ElasticEmail\ElasticEmailV2;
use ElasticEmail\Exceptions\ElasticEmailV2Exception;

/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 7/30/16
 */
class ElasticEmailV2Test extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function throws_missing_api_key_exception()
    {
        $this->setExpectedException(ElasticEmailV2Exception::class, 'Missing API key.');

        new ElasticEmailV2(null);
    }
}
