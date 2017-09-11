<?php

return [
    'services' => [
        [
            'id' => 'kickbox.io',
            'name' => 'Kickbox',
            'uri' => 'https://api.kickbox.io/v2/verify?email={EMAIL}&apikey={API_KEY}',
            'request_type' => 'GET',
            'fields' => [ 'api_key' ],
            'result_xpath' => '$.result',
            'result_map' => [ 'deliverable' => 'deliverable', 'undeliverable' => 'undeliverable', 'risky' => 'risky', 'unknown' => 'unknown' ]
        ], [
            'id' => 'thechecker.co',
            'name' => 'TheChecker',
            'uri' => 'https://api.thechecker.co/v1/verify?email={EMAIL}&api_key={API_KEY}',
            'request_type' => 'GET',
            'fields' => [ 'api_key', 'api_secret_key' ],
            'result_xpath' => '$.result',
            'result_map' => [ 'deliverable' => 'deliverable', 'undeliverable' => 'undeliverable', 'risky' => 'risky', 'unknown' => 'unknown' ]
        ], [
            'id' => 'verify-email.org',
            'name' => 'verify-email.org',
            'uri' => 'http://api.verify-email.org/api.php?usr={USERNAME}&pwd={PASSWORD}&check={EMAIL}',
            'request_type' => 'GET',
            'fields' => [ 'username', 'password' ],
            'result_xpath' => '$.verify_status',
            'result_map' => [ '1' => 'deliverable', '0' => 'undeliverable' ]
        ]
    ]
];
