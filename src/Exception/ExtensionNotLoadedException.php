<?php

declare(strict_types=1);

namespace Laminas\I18n\Exception;

use DomainException;

/** @final */
class ExtensionNotLoadedException extends DomainException implements ExceptionInterface
{
}
