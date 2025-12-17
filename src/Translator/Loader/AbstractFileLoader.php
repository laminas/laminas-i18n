<?php

declare(strict_types=1);

namespace Laminas\I18n\Translator\Loader;

use function is_file;
use function is_readable;
use function stream_resolve_include_path;

/**
 * Abstract file loader implementation; provides facilities around resolving
 * files via the include_path.
 */
abstract class AbstractFileLoader implements FileLoaderInterface
{
    /**
     * Whether or not to consult the include_path when locating files
     */
    private bool $useIncludePath = false;

    /**
     * Indicate whether or not to use the include_path to resolve translation files
     *
     * @return $this
     */
    public function setUseIncludePath(bool $flag = true): static
    {
        $this->useIncludePath = $flag;
        return $this;
    }

    /**
     * Are we using the include_path to resolve translation files?
     */
    public function useIncludePath(): bool
    {
        return $this->useIncludePath;
    }

    /**
     * Resolve a translation file
     *
     * Checks if the file exists and is readable, returning a boolean false if not; if the "useIncludePath"
     * flag is enabled, it will attempt to resolve the file from the
     * include_path if the file does not exist on the current working path.
     *
     * @param non-empty-string $filename
     * @return non-empty-string|false
     */
    protected function resolveFile(string $filename): string|false
    {
        if (! is_file($filename) || ! is_readable($filename)) {
            if (! $this->useIncludePath()) {
                return false;
            }
            return $this->resolveViaIncludePath($filename);
        }
        return $filename;
    }

    /**
     * Resolve a translation file via the include_path
     *
     * @param non-empty-string $filename
     * @return non-empty-string|false
     */
    protected function resolveViaIncludePath(string $filename): string|false
    {
        $resolvedIncludePath = stream_resolve_include_path($filename);
        if (
            $resolvedIncludePath === false
            ||
            ! is_file($resolvedIncludePath)
            ||
            ! is_readable($resolvedIncludePath)
            ||
            $resolvedIncludePath === ''
        ) {
            return false;
        }

        return $resolvedIncludePath;
    }
}
