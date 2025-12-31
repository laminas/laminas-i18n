<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader;

use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;

use function array_shift;
use function assert;
use function count;
use function explode;
use function fclose;
use function fopen;
use function fread;
use function fseek;
use function is_array;
use function is_int;
use function is_string;
use function sprintf;
use function strtolower;
use function trim;
use function unpack;

/**
 * Loads translation files in gettext format
 */
final readonly class Gettext extends AbstractFileLoader
{
    /**
     * @throws Exception\InvalidArgumentException
     */
    public function load(string $locale, string $filename): TextDomain|null
    {
        $resolvedFile = $this->resolveFile($filename);
        if ($resolvedFile === false) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not find or open file %s for reading',
                $filename,
            ));
        }

        $textDomain = [];

        $file = fopen($resolvedFile, 'rb');
        if ($file === false) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not open file %s for reading',
                $filename,
            ), 0);
        }

        $littleEndian = $this->isLittleEndian($file, $filename);
        $this->assertSupportedVersion($file, $littleEndian, $filename);

        // Gather main information
        $numStrings                   = $this->readInteger($file, $littleEndian);
        $originalStringTableOffset    = $this->readInteger($file, $littleEndian);
        $translationStringTableOffset = $this->readInteger($file, $littleEndian);

        // Usually there follow size and offset of the hash table, but we have
        // no need for it, so we skip them.
        fseek($file, $originalStringTableOffset);
        $originalStringTable = $this->readIntegerList($file, $littleEndian, 2 * $numStrings);

        fseek($file, $translationStringTableOffset);
        $translationStringTable = $this->readIntegerList($file, $littleEndian, 2 * $numStrings);

        // Read in all translations
        for ($current = 0; $current < $numStrings; $current++) {
            $sizeKey                 = $current * 2 + 1;
            $offsetKey               = $current * 2 + 2;
            $originalStringSize      = $originalStringTable[$sizeKey];
            $originalStringOffset    = $originalStringTable[$offsetKey];
            $translationStringSize   = $translationStringTable[$sizeKey];
            $translationStringOffset = $translationStringTable[$offsetKey];

            assert(
                is_int($originalStringSize)
                &&
                is_int($originalStringOffset)
                &&
                is_int($translationStringSize)
                &&
                is_int($translationStringOffset),
            );

            $originalString = [''];
            if ($originalStringSize > 0) {
                fseek($file, $originalStringOffset);
                $originalString = explode("\0", $this->read($file, $originalStringSize));
            }

            if ($translationStringSize > 0) {
                fseek($file, $translationStringOffset);
                $translationString = explode("\0", $this->read($file, $translationStringSize));

                if (isset($originalString[1], $translationString[1])) {
                    $textDomain[$originalString[0]] = $translationString;

                    array_shift($originalString);

                    foreach ($originalString as $string) {
                        if (! isset($textDomain[$string])) {
                            $textDomain[$string] = '';
                        }
                    }
                } else {
                    $textDomain[$originalString[0]] = $translationString[0];
                }
            }
        }

        fclose($file);

        $pluralRule = null;
        // Read header entries
        if (isset($textDomain['']) && is_string($textDomain[''])) {
            $rawHeaders = explode("\n", trim($textDomain['']));

            foreach ($rawHeaders as $rawHeader) {
                $data = explode(':', $rawHeader, 2);
                assert(count($data) === 2);
                [$header, $content] = $data;
                if (strtolower(trim($header)) === 'plural-forms') {
                    $pluralRule = $content;
                }
            }

            unset($textDomain['']);
        }

        $textDomain = new TextDomain($textDomain);
        if (is_string($pluralRule) && $pluralRule !== '') {
            $textDomain->setPluralRule(PluralRule::fromString($pluralRule));
        }

        return $textDomain;
    }

    /**
     * Read a single integer from the current file.
     *
     * @param resource $file
     */
    private function readInteger($file, bool $littleEndian): int
    {
        $value = $this->read($file, 4);

        if ($littleEndian) {
            $result = unpack('Vint', $value);
        } else {
            $result = unpack('Nint', $value);
        }

        assert(is_array($result));

        $integer = $result['int'] ?? null;

        assert(is_int($integer));

        return $integer;
    }

    /**
     * Read an integer from the current file.
     *
     * @param resource $file
     */
    private function readIntegerList($file, bool $littleEndian, int $num): array
    {
        $value = $this->read($file, 4 * $num);

        $result = $littleEndian
            ? unpack('V' . $num, $value)
            : unpack('N' . $num, $value);

        assert(is_array($result));

        return $result;
    }

    /** @param resource $file */
    private function read($file, int $bytes): string
    {
        $content = fread($file, $bytes);
        assert($content !== false);

        return $content;
    }

    /**
     * @param resource $file
     * @throws Exception\InvalidArgumentException
     */
    private function isLittleEndian($file, string $filename): bool
    {
        // Verify magic number
        $magic = $this->read($file, 4);

        if ($magic === "\x95\x04\x12\xde") {
            return false;
        }

        if ($magic === "\xde\x12\x04\x95") {
            return true;
        }

        fclose($file);
        throw new Exception\InvalidArgumentException(sprintf(
            '%s is not a valid gettext file',
            $filename
        ));
    }

    /**
     * Verify major revision (only 0 and 1 supported)
     *
     * @param resource $file
     * @throws Exception\InvalidArgumentException
     */
    private function assertSupportedVersion($file, bool $littleEndian, string $filename): void
    {
        $majorRevision = $this->readInteger($file, $littleEndian) >> 16;

        if ($majorRevision === 0 || $majorRevision === 1) {
            return;
        }

        fclose($file);
        throw new Exception\InvalidArgumentException(sprintf(
            '%s has an unknown major revision',
            $filename,
        ));
    }
}
