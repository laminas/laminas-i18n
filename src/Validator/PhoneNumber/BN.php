<?php

return [
    'code' => '673',
    'patterns' => [
        'national' => [
            'general' => '/^[2-578]\\d{6}$/',
            'fixed' => '/^[2-5]\\d{6}$/',
            'mobile' => '/^[78]\\d{6}$/',
            'emergency' => '/^99[135]$/',
        ],
        'possible' => [
            'general' => '/^\\d{7}$/',
            'emergency' => '/^\\d{3}$/',
        ],
    ],
];
