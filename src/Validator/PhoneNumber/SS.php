<?php

return [
    'code' => '211',
    'patterns' => [
        'national' => [
            'general' => '/^[19]\\d{8}$/',
            'fixed' => '/^18\\d{7}$/',
            'mobile' => '/^(?:12|9[1257])\\d{7}$/',
        ],
        'possible' => [
            'general' => '/^\\d{9}$/',
        ],
    ],
];
