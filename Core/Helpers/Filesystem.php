<?php

namespace Silmaril\Core\Helpers;

class Filesystem
{
    /**
     * Get file PHP path
     * 
     * @param string $path
     * @param string $separator
     * @return string
     */
    public static function phpFile(string $path, string $separator = '/'): string
    {
        return static::file($path, $separator) . '.php';
    }

    /**
     * Get file path
     * 
     * @param string $path
     * @param string $separator
     * @return string
     */
    public static function file(string $path, string $separator = '/'): string
    {
        $path = \str_replace($separator, DIRECTORY_SEPARATOR, $path);

        return \get_theme_file_path(\rtrim($path, DIRECTORY_SEPARATOR));
    }
}
