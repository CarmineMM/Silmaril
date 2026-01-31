<?php

namespace Silmaril\Core\Foundation\Cache;

use Silmaril\Core\Foundation\Theme;

abstract class CacheGenerator
{
    /**
     * Instancia del tema
     */
    protected Theme $theme;

    /**
     * Cache service
     */
    protected CacheService $cacheService;

    /**
     * Constructor
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;

        $this->cacheService = new CacheService($theme);
    }

    /**
     * Generar cache
     */
    abstract public function generate(): bool;

    /**
     * Escribir contenido a archivo de cache
     */
    protected function writeCacheFile(string $component, string $content): bool
    {
        $cacheFile = $this->cacheService->getCacheFilePath($component);


        // Añadir header PHP y timestamp
        $header = "<?php\n";
        $header .= "/**\n";
        $header .= " * Cache File - {$component} - Silmaril {$this->theme->getVersion()}\n";
        $header .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
        $header .= " * DO NOT EDIT - This file is auto-generated\n";
        $header .= " */\n\n";

        $content = $header . $content;

        return \file_put_contents($cacheFile, $content) !== false;
    }

    /**
     * Formatear array como código PHP
     */
    protected function formatArrayAsPhp(array $data, int $indent = 0): string
    {
        $indentStr = str_repeat('    ', $indent);
        $output = "[\n";

        foreach ($data as $key => $value) {
            $output .= $indentStr . '    ';

            // Key
            if (\is_string($key)) {
                $output .= "'" . \addslashes($key) . "' => ";
            } elseif (\is_numeric($key)) {
                $output .= "{$key} => ";
            }

            // Value
            if (\is_array($value)) {
                $output .= $this->formatArrayAsPhp($value, $indent + 1);
            } elseif (\is_string($value)) {
                $output .= "'" . \addslashes($value) . "'";
            } elseif (\is_bool($value)) {
                $output .= $value ? 'true' : 'false';
            } elseif ($value === null) {
                $output .= 'null';
            } else {
                $output .= $value;
            }

            $output .= ",\n";
        }

        $output .= "{$indentStr}]";

        return $output;
    }
}
