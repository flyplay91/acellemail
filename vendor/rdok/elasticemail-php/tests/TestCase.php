<?php

namespace Tests;

use Dotenv\Dotenv;
use ElasticEmail\ElasticEmailV2;
use ElasticEmail\V2\Responses\Email\EmailResponse;
use Faker\Factory;
use PHPUnit_Framework_TestCase;

/**
 * @author Rizart Dokollari <r.dokollari@gmail.com>
 * @since 6/5/16
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticEmailV2
     */
    protected $elasticEmail;

    /**
     * @var array
     */
    protected $emailData;
    protected $faker;

    public function setUp()
    {
        parent::setUp();

        if (getenv('APP_ENV') !== 'travis-ci') {
            $dotEnv = new Dotenv(__DIR__.'/..');
            $dotEnv->load();
        }

        $this->faker = Factory::create();
        $this->elasticEmail = new ElasticEmailV2(getenv('ELASTIC_EMAIL_API_KEY'));

        $this->emailData = [
            'from'      => 'r.dokollari@gmail.com',
            'from_name' => 'From Name',
            'to'        => 'r.dokollari@gmail.com',
            'subject'   => 'Subject',
            'body_html' => "<p>Body Html</p><hr>",
            'body_text' => 'Body Text',
        ];
    }

    /**
     * @return EmailResponse
     */
    protected function sendSuccessfulEmail()
    {
        $response = $this->elasticEmail->email()->send([
            'to'      => getenv('SINGLE_TESTER_EMAIL'),
            'subject' => getenv('EMAIL_SUBJECT'),
            'from'    => getenv('SINGLE_TESTER_EMAIL')
        ]);

        return $response;
    }
}
