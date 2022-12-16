<?php

declare(strict_types=1);

namespace Laminas\I18n\Geography;

use Countable;
use IteratorAggregate;
use Laminas\I18n\CountryCode;

/**
 * @extends IteratorAggregate<array-key, CountryCode>
 */
interface CountryCodeListInterface extends IteratorAggregate, Countable
{
}
