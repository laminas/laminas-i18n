<?php

return [
    'code' => '379',
    'patterns' => [
        'national' => [
            'general' => '/^06\\d{8}$/',
            'fixed' => '/^06698\\d{5}$/',
            'mobile' => '/^N/A$/',
            'emergency' => '/^11[2358]$/',
        ],
        'possible' => [
            'general' => '/^\\d{10}$/',
            'mobile' => '/^N/A$/',
            'emergency' => '/^\\d{3}$/',
        ],
    ],
];
