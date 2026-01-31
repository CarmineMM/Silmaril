<?php

namespace Silmaril\Core\Foundation\Cache;

use Illuminate\Support\Arr;
use Silmaril\Core\Helpers\Filesystem;

class ConfigCacheGenerator extends CacheGenerator
{
    /**
     * Generar cache de configuraciones
     */
    public function generate(): bool
    {
        $configFiles = Filesystem::getFilesInFolder($this->cacheService->theme->getConfigPath(), 'php');


        $cacheContent = "// Config Cache\n";
        $cacheContent .= "// All configuration files combined\n\n";
        $cacheContent .= "return [\n";

        foreach ($configFiles as $file) {
            $filename = basename($file, '.php');
            $configData = require $file;

            $cacheContent .= "    '{$filename}' => " . $this->formatArrayAsPhp($configData) . ",\n\n";
        }

        $cacheContent .= "];\n";

        return $this->writeCacheFile('config', $cacheContent);
    }
}
