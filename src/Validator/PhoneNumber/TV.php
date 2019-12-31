<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

return array(
    'code' => '688',
    'patterns' => array(
        'national' => array(
            'general' => '/^[29]\\d{4,5}$/',
            'fixed' => '/^2[02-9]\\d{3}$/',
            'mobile' => '/^90\\d{4}$/',
            'emergency' => '/^911$/',
        ),
        'possible' => array(
            'general' => '/^\\d{5,6}$/',
            'fixed' => '/^\\d{5}$/',
            'mobile' => '/^\\d{6}$/',
            'emergency' => '/^\\d{3}$/',
        ),
    ),
);
