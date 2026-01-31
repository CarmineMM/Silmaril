<?php

namespace Silmaril\Core\Foundation\Cache;

use Silmaril\Core\Helpers\Filesystem;

class ConfigCacheGenerator extends CacheGenerator
{
    /**
     * Generar cache de configuraciones
     */
    public function generate(): bool
    {
        $configFiles = Filesystem::getFilesInFolder($this->theme->getConfigPath(), 'php');

        $cacheContent = "// Config Cache\n";
        $cacheContent .= "// All configuration files combined\n\n";
        $cacheContent .= "return [\n";

        foreach ($configFiles as $file) {
            $filename = basename($file, '.php');
            $configData = require $file;

            $cacheContent .= "    '{$filename}' => " . $this->formatArrayAsPhp($configData, 1) . ",\n\n";
        }

        $cacheContent .= "];\n";

        return $this->writeCacheFile('config', $cacheContent);
    }
}
