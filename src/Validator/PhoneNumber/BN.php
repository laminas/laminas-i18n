<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

return array(
    'code' => '673',
    'patterns' => array(
        'national' => array(
            'general' => '/^[2-578]\\d{6}$/',
            'fixed' => '/^[2-5]\\d{6}$/',
            'mobile' => '/^[78]\\d{6}$/',
            'emergency' => '/^99[135]$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
