<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

return array(
    'code' => '236',
    'patterns' => array(
        'national' => array(
            'general' => '/^[278]\\d{7}$/',
            'fixed' => '/^2[12]\\d{6}$/',
            'mobile' => '/^7[0257]\\d{6}$/',
            'premium' => '/^8776\\d{4}$/',
        ),
        'possible' => array(
            'general' => '/^\\d{8}$/',
        ),
    ),
);
