<?php

namespace Tests\integration\V2\Email;

use Tests\TestCase;

/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 6/5/16
 */
class SendResponseTest extends TestCase
{
    /**
     * @test
     * @vcr integration.email.send.response.valid_transaction_id.yml
     */
    public function returns_valid_transaction_id()
    {
        $this->markTestIncomplete();
        $response = $this->elasticEmail->email()->send($this->emailData);

        $this->assertSame(200, $response->getHttpClient()->getStatusCode());

        $this->assertTrue($response->wasSuccessful());

        $this->assertNull($response->getErrorMessage());

        $this->assertNotEmpty($response->getData());

        $this->assertNotEmpty($response->getTransactionId());
    }
}
