<?php

return [
    'code' => '245',
    'patterns' => [
        'national' => [
            'general' => '/^[3567]\\d{6}$/',
            'fixed' => '/^3(?:2[0125]|3[1245]|4[12]|5[1-4]|70|9[1-467])\\d{4}$/',
            'mobile' => '/^[5-7]\\d{6}$/',
            'emergency' => '/^11[378]$/',
        ],
        'possible' => [
            'general' => '/^\\d{7}$/',
            'emergency' => '/^\\d{3}$/',
        ],
    ],
];
