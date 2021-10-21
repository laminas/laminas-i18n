<?php

return [
    'code' => '683',
    'patterns' => [
        'national' => [
            'general' => '/^[1-5]\\d{3}$/',
            'fixed' => '/^[34]\\d{3}$/',
            'mobile' => '/^[125]\\d{3}$/',
            'emergency' => '/^999$/',
        ],
        'possible' => [
            'general' => '/^\\d{4}$/',
            'emergency' => '/^\\d{3}$/',
        ],
    ],
];
