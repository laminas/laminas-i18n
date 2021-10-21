<?php

return [
    'code' => '690',
    'patterns' => [
        'national' => [
            'general' => '/^[2-5]\\d{3}$/',
            'fixed' => '/^[2-4]\\d{3}$/',
            'mobile' => '/^5\\d{3}$/',
        ],
        'possible' => [
            'general' => '/^\\d{4}$/',
        ],
    ],
];
