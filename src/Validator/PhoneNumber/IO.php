<?php

return [
    'code' => '246',
    'patterns' => [
        'national' => [
            'general' => '/^3\\d{6}$/',
            'fixed' => '/^37\\d{5}$/',
            'mobile' => '/^38\\d{5}$/',
        ],
        'possible' => [
            'general' => '/^\\d{7}$/',
        ],
    ],
];
