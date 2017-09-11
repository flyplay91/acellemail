<?php

namespace Tests\unit\V2\Requests\Email;

use ElasticEmail\V2\Requests\Email\EmailRequest;
use ElasticEmail\V2\Requests\Email\RequestException;
use PHPUnit_Framework_TestCase;

/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 7/30/16
 */
class EmailRequestTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function throws_email_request_exception_if_base_uri_is_missing()
    {
        $this->setExpectedException(RequestException::class, 'Invalid base uri.');

        new EmailRequest(null, []);
    }

    /** @test */
    public function throws_email_request_exception_if_base_uri_is_invalid()
    {
        $this->setExpectedException(RequestException::class, 'Invalid base uri.');

        new EmailRequest('invalid', []);
    }
}
