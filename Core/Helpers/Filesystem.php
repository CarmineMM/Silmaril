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

    /**
     * Get folder path
     * 
     * @param string $path
     * @param string $separator
     * @return string
     */
    public static function folder(string $path, string $separator = '/'): string
    {
        return static::file($path, $separator);
    }

    /**
     * Get all files in a folder
     * 
     * @param string $folder
     * @param string $fileExtension
     * @return bool|string[]
     */
    public static function getFilesInFolder(string $folder, string $fileExtension = '*'): array
    {
        $folderPath = static::folder($folder);

        if ($fileExtension === '*') {
            return \glob("{$folderPath}/*");
        }

        return \glob("{$folderPath}/*.{$fileExtension}");
    }
}
