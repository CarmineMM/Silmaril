<?php

namespace Silmaril\Core\Foundation\Cache;

use Illuminate\Support\Arr;
use Silmaril\Core\Foundation\RoadTracer;
use Silmaril\Core\Foundation\Theme;
use Silmaril\Core\Helpers\Filesystem;

class CacheService
{
    /**
     * Configuración de cache
     */
    protected array $config = [];

    /**
     * Path de cache
     */
    protected string $cachePath;

    /**
     * Manifest file
     */
    protected string $manifestFile;

    /**
     * Constructor
     */
    public function __construct(
        public Theme $theme
    ) {
        $this->config = $theme->config('cache', []);
        $this->cachePath = $this->config['path'] ?? Filesystem::folder('bootstrap/cache');
        $this->manifestFile = $this->cachePath . DIRECTORY_SEPARATOR . 'manifest';

        // Crear directorio de cache si no existe
        Filesystem::createFolderIfNoExists($this->cachePath);

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Cache/CacheService'),
            'line' => 35,
            'function' => '__construct',
            'class' => CacheService::class,
            'method' => CacheService::class . '->__construct()',
            'object' => CacheService::class,
            'args' => [],
        ]);
    }

    /**
     * Verificar si cache está habilitada
     */
    public function isEnabled(): bool
    {
        return Arr::get($this->config, 'enabled', false);
    }

    /**
     * Verificar si cache existe y es válida
     */
    public function cacheExistsAndValid(string $component): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $cacheFile = $this->getCacheFilePath($component);

        if (!\file_exists($cacheFile)) {
            return false;
        }

        // Verificar lifetime
        if (Arr::get($this->config, 'lifetime', null) !== null) {
            $fileAge = time() - \filemtime($cacheFile);
            if ($fileAge > Arr::get($this->config, 'lifetime', null)) {
                return false;
            }
        }

        // Verificar auto-regenerate
        if (Arr::get($this->config, 'auto_regenerate', false)) {
            if (!$this->isCacheUpToDate($component)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtener path del archivo de cache
     */
    public function getCacheFilePath(string $component): string
    {
        return $this->cachePath . DIRECTORY_SEPARATOR . $component . '.php';
    }

    /**
     * Cargar cache para un componente
     */
    public function loadCache(string $component): mixed
    {
        $cacheFile = $this->getCacheFilePath($component);

        if (!\file_exists($cacheFile)) {
            return null;
        }

        return require $cacheFile;
    }

    /**
     * Generar toda la cache
     */
    public function generateAll(): array
    {
        $results = [];
        $components = Arr::get($this->config, 'components', []);

        foreach ($components as $component => $enabled) {
            if ($enabled) {
                $results[$component] = $this->generateComponent($component);
            }
        }

        // Actualizar manifest
        $this->updateManifest();

        return $results;
    }

    /**
     * Generar cache para un componente específico
     */
    public function generateComponent(string $component): bool
    {
        foreach ($this->namesGenerator($component) as $componentClass) {
            $generatorClass = $componentClass;

            if (!\class_exists($generatorClass)) {
                continue;
            }

            $generator = new $generatorClass($this->theme);

            return $generator->generate();
        }

        return false;
    }

    private function namesGenerator(string $component): array
    {
        return \array_map(
            fn($generator) =>  str_replace('{component}', $component, $generator),
            $this->config['namespace_cache_genetors']
        );
    }

    /**
     * Limpiar toda la cache
     */
    public function clearAll(): bool
    {
        $cacheFiles = Filesystem::getFilesInFolder($this->cachePath, 'php', false);

        foreach ($cacheFiles as $file) {
            if (\is_file($file)) {
                \unlink($file);
            }
        }

        // Eliminar manifest
        if (\file_exists($this->manifestFile)) {
            \unlink($this->manifestFile);
        }

        return true;
    }

    /**
     * Limpiar cache de un componente específico
     */
    public function clearComponent(string $component): bool
    {
        $cacheFile = $this->getCacheFilePath($component);

        if (\file_exists($cacheFile)) {
            return \unlink($cacheFile);
        }

        return false;
    }

    /**
     * Verificar si la cache está actualizada
     */
    protected function isCacheUpToDate(string $component): bool
    {
        $manifest = $this->getManifest();

        if (!isset($manifest[$component])) {
            return false;
        }

        $cacheFile = $this->getCacheFilePath($component);
        $cacheTime = \filemtime($cacheFile);

        // Verificar si hay archivos de configuración más nuevos
        // TODO: Revisar luego...
        $configFiles = $this->getConfigFilesForComponent($component);

        // foreach ($configFiles as $file) {
        if (\file_exists($configFiles) && \filemtime($configFiles) > $cacheTime) {
            return false;
        }
        // }

        return true;
    }

    /**
     * Obtener archivos de configuración para un componente
     */
    protected function getConfigFilesForComponent(string $component): string
    {
        return Filesystem::file($this->theme->getConfigPath() . "/{$component}.php");
    }

    /**
     * Obtener manifest
     */
    public function getManifest(): array
    {
        if (!\file_exists($this->manifestFile)) {
            return [];
        }

        return \json_decode(\file_get_contents($this->manifestFile), true) ?? [];
    }

    /**
     * Actualizar manifest
     */
    protected function updateManifest(): void
    {
        $manifest = [
            'generated_at' => time(),
            'cache_key' => $this->config['key'] ?? 'silmaril',
            'components' => [],
        ];

        $components = $this->config['components'] ?? [];

        foreach ($components as $component => $enabled) {
            if ($enabled) {
                $cacheFile = $this->getCacheFilePath($component);
                if (\file_exists($cacheFile)) {
                    $manifest['components'][$component] = [
                        'file' => $cacheFile,
                        'mtime' => \filemtime($cacheFile),
                        'size' => \filesize($cacheFile),
                        'hash' => \md5_file($cacheFile),
                    ];
                }
            }
        }

        \file_put_contents($this->manifestFile, \json_encode($manifest, JSON_PRETTY_PRINT));
    }

    /**
     * Obtener estadísticas de cache
     */
    public function getStats(): array
    {
        $manifest = $this->getManifest();
        $stats = [
            'enabled' => $this->isEnabled(),
            'path' => $this->cachePath,
            'generated_at' => $manifest['generated_at'] ?? null,
            'components' => [],
            'total_size' => 0,
        ];

        if (isset($manifest['components'])) {
            foreach ($manifest['components'] as $component => $data) {
                $stats['components'][$component] = [
                    'exists' => \file_exists($data['file']),
                    'size' => $data['size'],
                    'mtime' => $data['mtime'],
                ];
                $stats['total_size'] += $data['size'];
            }
        }

        return $stats;
    }

    /**
     * Forzar regeneración de cache
     */
    public function forceRegenerate(): array
    {
        $this->clearAll();

        return $this->generateAll();
    }
}
