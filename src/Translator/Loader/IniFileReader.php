<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader;

use Laminas\I18n\Exception\RuntimeException;

use function array_filter;
use function array_merge_recursive;
use function array_shift;
use function assert;
use function count;
use function explode;
use function is_array;
use function is_file;
use function is_readable;
use function is_string;
use function parse_ini_file;
use function parse_ini_string;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function str_contains;

use const E_WARNING;
use const INI_SCANNER_NORMAL;
use const INI_SCANNER_TYPED;

final readonly class IniFileReader
{
    /** @param non-empty-string $nestSeparator */
    public function __construct(
        private string $nestSeparator = '.',
        private bool $processSections = true,
        private bool $typed = false,
    ) {
    }

    /**
     * @param non-empty-string $filename
     * @return array<array-key, mixed>
     * @throws RuntimeException
     */
    public function read(string $filename): array
    {
        if (! is_file($filename) || ! is_readable($filename)) {
            throw new RuntimeException(sprintf(
                "File '%s' doesn't exist or is not readable",
                $filename,
            ));
        }

        set_error_handler(
            function ($error, $message = '') {
                throw new RuntimeException(
                    sprintf('Error reading INI file: %s', $message),
                    $error,
                );
            },
            E_WARNING,
        );

        try {
            $ini = parse_ini_file(
                $filename,
                $this->processSections,
                $this->typed ? INI_SCANNER_TYPED : INI_SCANNER_NORMAL,
            );
        } finally {
            restore_error_handler();
        }

        assert($ini !== false);

        return $this->process($ini);
    }

    /**
     * @return array<array-key, mixed>
     * @throws RuntimeException
     */
    public function readString(string $string): array
    {
        if (empty($string)) {
            return [];
        }

        set_error_handler(
            function ($error, $message = '') {
                throw new RuntimeException(
                    sprintf('Error reading INI string: %s', $message),
                    $error,
                );
            },
            E_WARNING,
        );

        try {
            $ini = parse_ini_string(
                $string,
                $this->processSections,
                $this->typed ? INI_SCANNER_TYPED : INI_SCANNER_NORMAL,
            );
        } finally {
            restore_error_handler();
        }

        if ($ini === false) {
            throw new RuntimeException('The data could not be parsed as an ini file');
        }

        return $this->process($ini);
    }

    /**
     * Process data from the parsed ini file.
     *
     * @param  array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    private function process(array $data): array
    {
        $config = [];

        foreach ($data as $section => $value) {
            if (! is_array($value)) {
                $this->processKey($section, $value, $config);

                continue;
            }

            if (is_string($section) && str_contains($section, $this->nestSeparator)) {
                $sections = explode($this->nestSeparator, $section);
                $config   = array_merge_recursive($config, $this->buildNestedSection($sections, $value));

                continue;
            }

            $config[$section] = $this->processSection($value);
        }

        return $config;
    }

    /**
     * Process a nested section
     *
     * @param list<string> $sections
     * @param array<array-key, mixed> $value
     * @return array<array-key, mixed>
     */
    private function buildNestedSection(array $sections, array $value): array
    {
        if (count($sections) === 0) {
            return $this->processSection($value);
        }

        $nestedSection = [];

        $first                 = array_shift($sections);
        $nestedSection[$first] = $this->buildNestedSection($sections, $value);

        return $nestedSection;
    }

    /**
     * Process a section
     *
     * @param array<array-key, mixed> $section>
     * @return array<array-key, mixed>
     */
    protected function processSection(array $section): array
    {
        $config = [];

        /** @psalm-var mixed $value */
        foreach ($section as $key => $value) {
            $this->processKey($key, $value, $config);
        }

        return $config;
    }

    /**
     * Process a key.
     *
     * @throws RuntimeException
     */
    protected function processKey(int|string $key, mixed $value, array &$config): void
    {
        $key = (string) $key;
        if (str_contains($key, $this->nestSeparator)) {
            $pieces = explode($this->nestSeparator, $key, 2);

            $nonEmpty = array_filter($pieces, static fn (string $item): bool => $item !== '');

            if (count($nonEmpty) !== 2) {
                throw new RuntimeException(sprintf('Invalid key "%s"', $key));
            }

            assert(count($pieces) === 2);

            if (! isset($config[$pieces[0]])) {
                if ($pieces[0] === '0' && ! empty($config)) {
                    $config = [$pieces[0] => $config];
                } else {
                    $config[$pieces[0]] = [];
                }
            }

            assert(is_array($config[$pieces[0]]));

            /** @psalm-suppress MixedArgument */
            $this->processKey($pieces[1], $value, $config[$pieces[0]]);

            return;
        }

        /** @psalm-suppress MixedAssignment */
        $config[$key] = $value;
    }
}
