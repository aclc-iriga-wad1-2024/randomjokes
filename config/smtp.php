<?php
/**
 * SMTP Configuration
 */

$smtp = [
    'host'     => 'localhost',
    'auth'     => false,
    'username' => 'randomjokes@localhost.net',
    'password' => '123456',
    'secure'   => '',
    'port'     => 25,
    'debug'    => 0,
    'from'     => [
        'email' => 'randomjokes@localhost.net',
        'name'  => 'RandomJokes'
    ],
    'options'  => [
        'ssl'  => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ]
];