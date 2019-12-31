<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

return array(
    'code' => '241',
    'patterns' => array(
        'national' => array(
            'general' => '/^[01]\\d{6,7}$/',
            'fixed' => '/^1\\d{6}$/',
            'mobile' => '/^0[2-7]\\d{6}$/',
            'emergency' => '/^1730|18|13\\d{2}$/',
        ),
        'possible' => array(
            'general' => '/^\\d{7,8}$/',
            'emergency' => '/^\\d{2,4}$/',
        ),
    ),
);
