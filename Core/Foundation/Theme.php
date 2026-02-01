<?php

namespace Silmaril\Core\Foundation;

use Exception;
use Illuminate\Support\Arr;
use Silmaril\Core\Exceptions\ServiceProviderNotFound;
use Silmaril\Core\Foundation\Cache\CacheService;
use Silmaril\Core\Foundation\RoadTracer;
use Silmaril\Core\Helpers\Filesystem;

class Theme
{
    /**
     * Versión del tema
     */
    protected string $version = '1.0.0';

    /**
     * Theme name
     * 
     * @var string
     */
    const NAME = 'Silmaril';

    /**
     * Instancia única (Singleton)
     */
    private static ?Theme $instance = null;

    /**
     * Service providers registrados
     */
    protected array $providers = [];

    /**
     * Servicios del tema
     */
    protected array $services = [];

    /**
     * Configuración del tema
     */
    protected array $config = [];

    /**
     * Configuración diferida (Deferred)
     */
    private array $deferredConfig = [
        'post_types',
        'taxonomies',
    ];

    /**
     * Path using dot notation
     * 
     * @var string
     */
    private string $configPath = 'App/config';

    /**
     * Cache service
     */
    protected ?CacheService $cacheService = null;

    /**
     * Constructor privado (Singleton)
     */
    private function __construct()
    {
        $this->loadConstants();

        $this->loadThemeInWordpress();

        $this->cacheService = new CacheService($this);

        $this->loadConfiguration();

        $this->registerProviders();

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Code/Foundation/Theme'),
            'line' => 45,
            'function' => '__construct',
            'class' => Theme::class,
            'method' => Theme::class . '->__construct()',
            'object' => Theme::class,
            'args' => [],
        ]);
    }

    /**
     * Indica si el tema ha sido inicializado
     * 
     * @return bool
     */
    public static function hasInizialice(): bool
    {
        return (bool) self::$instance;
    }

    /**
     * Obtener instancia única
     */
    public static function getInstance(): Theme
    {
        if (self::hasInizialice()) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::$instance;
    }

    /**
     * Carga cosas asociadas al tema que relacionan con Wordpress
     * 
     * @return void
     */
    public function loadThemeInWordpress(): void
    {
        $this->version = \wp_get_theme()->get('Version');
    }

    /**
     * Get teheme version
     * 
     * @return string
     */
    public function getVersion(): string
    {
        return self::getInstance()->version;
    }

    /**
     * Carga constantes para PHP
     * 
     * @return void
     */
    private function loadConstants(): void
    {
        if (!\defined('DIRECTORY_SEPARATOR')) {
            \define('DIRECTORY_SEPARATOR', '/');
        }

        if (!\defined('TEXT_DOMAIN')) {
            \define('TEXT_DOMAIN', \wp_get_theme()->get('TextDomain'));
        }
    }

    /**
     * Cargar configuración desde archivos
     */
    private function loadConfiguration(): void
    {
        if ($this->cacheService !== null && $this->cacheService->isEnabled()) {
            $this->config = $this->cacheService->getConfig();
            return;
        }

        $files = Filesystem::getFilesInFolder($this->configPath, 'php');

        foreach ($files as $filePath) {
            $fileName = \basename($filePath, '.php');

            // Ignorar si está en la lista de diferidos
            if (\in_array($fileName, $this->deferredConfig)) {
                continue;
            }

            // Ignorar si el nombre del archivo contiene 'deferred'
            if (\str_contains($fileName, 'deferred')) {
                continue;
            }

            $this->config[$fileName] = require_once $filePath;
        }
    }

    /**
     * Carga de configuracion diferida
     * 
     * @return void
     */
    public function loadDeferredConfig(): void
    {
        if ($this->cacheService->isEnabled()) {
            return;
        }

        $files = Filesystem::getFilesInFolder($this->configPath, 'php');

        foreach ($files as $filePath) {
            $fileName = \basename($filePath, '.php');
            $isDeferred = false;

            // Verificar si está en la lista de diferidos
            if (\in_array($fileName, $this->deferredConfig)) {
                $isDeferred = true;
            }

            // Verificar si el nombre contiene 'deferred'
            if (\str_contains($fileName, 'deferred')) {
                $isDeferred = true;
            }

            if ($isDeferred) {
                $this->config[$fileName] = require_once $filePath;
            }
        }

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Theme'),
            'line' => 166,
            'function' => 'loadDeferredConfig',
            'class' => Theme::class,
            'method' => Theme::class . '->loadDeferredConfig()',
            'object' => Theme::class,
            'args' => [],
        ]);
    }

    /**
     * Obtener configuración
     */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * Verifica si alguna de las configuraciones es true
     * 
     * @param array $keys
     * @return bool
     */
    public function anyConfigIsTrue(array $keys): bool
    {
        foreach ($keys as $key) {
            if ($this->config($key, false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Registrar providers desde configuración, y los instancia (Ejecuta su __construct)
     */
    private function registerProviders(): void
    {
        if ($this->cacheService->isEnabled()) {
            $this->providers = $this->cacheService->getConfig('providers.auto');
            return;
        }

        $providersConfig = $this->config('providers', []);

        if (isset($providersConfig['auto'])) {
            $this->providers = Arr::map($providersConfig['auto'], function ($provider) {
                if (!\class_exists($provider)) {
                    throw new ServiceProviderNotFound("Service Provider {$provider} not found");
                }

                return $provider;
            });
        }

        if (isset($providersConfig['deferred'])) {
            // Providers diferidos se cargarán bajo demanda
        }
    }

    /**
     * Bootear todos los providers
     */
    public function bootstrap(): void
    {
        // Init Providers
        $this->instanceRegisterProviders();

        // Init Services
        $this->initServices();

        // Boot the providers
        $this->bootProviders();

        // Cargar cache en caso de existir
        if ($this->cacheService->requireGenerate() && $this->config('cache.enabled')) {
            $this->cacheService->loadConfig();
            $this->cacheService->generateAll();
            $this->cacheService->updateDBCachePath();
        }

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Theme'),
            'line' => 216,
            'function' => 'bootstrap',
            'class' => Theme::class,
            'method' => Theme::class . '->bootstrap()',
            'object' => Theme::class,
            'args' => [],
        ]);
    }

    /**
     * Registrar un servicio
     */
    public function registerService(string $name, Service $service): void
    {
        $this->services[$name] = $service;
    }

    /**
     * Remove a service
     */
    public function removeService(string $name): void
    {
        unset($this->services[$name]);
    }

    /**
     * Init Providers
     */
    public function instanceRegisterProviders(): void
    {
        foreach ($this->providers as $key => $providerClass) {
            $this->providers[$key] = new $providerClass($this);

            $this->providers[$key]->register();

            RoadTracer::stroke([
                'file' => Filesystem::phpFile('Core/Foundation/Theme'),
                'line' => 192,
                'function' => 'register',
                'class' => $this->providers[$key]::class,
                'method' => $this->providers[$key]::class . '->register()',
                'object' => $this->providers[$key]::class,
                'args' => [],
            ]);
        }

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Theme'),
            'line' => 214,
            'function' => 'instanceRegisterProviders',
            'class' => Theme::class,
            'method' => Theme::class . '->instanceRegisterProviders()',
            'object' => Theme::class,
            'args' => [],
        ]);
    }

    /**
     * Boot the providers
     * 
     * @return void
     */
    public function bootProviders(): void
    {
        foreach ($this->providers as $providerClass) {
            $providerClass->boot();

            RoadTracer::stroke([
                'file' => Filesystem::phpFile('Core/Foundation/Theme'),
                'line' => 261,
                'function' => 'boot',
                'class' => $providerClass::class,
                'method' => $providerClass::class . '->boot()',
                'object' => $providerClass::class,
                'args' => [],
            ]);
        }

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Theme'),
            'line' => 214,
            'function' => 'bootProviders',
            'class' => Theme::class,
            'method' => Theme::class . '->bootProviders()',
            'object' => Theme::class,
            'args' => [],
        ]);
    }

    /**
     * Obtener un servicio
     */
    public function initServices(): void
    {
        foreach ($this->services as $key => $service) {
            $service->init();

            RoadTracer::stroke([
                'file' => Filesystem::phpFile('Core/Foundation/Theme'),
                'line' => 263,
                'function' => 'init',
                'class' => $service::class,
                'method' => $service::class . '->init()',
                'object' => $service::class,
                'args' => [],
            ]);
        }

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Theme'),
            'line' => 278,
            'function' => 'initServices',
            'class' => Theme::class,
            'method' => Theme::class . '->initServices()',
            'object' => Theme::class,
            'args' => [],
        ]);
    }

    /**
     * Obtener un servicio
     */
    public function getService(string $name): Service
    {
        if (!isset($this->services[$name])) {
            throw new Exception("Service {$name} not found");
        }

        return $this->services[$name];
    }

    /**
     * Call Service Method, include life cycle
     * 
     * @param string $name
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function callServiceMethod(string $name, string $method, ...$args): mixed
    {
        $service = $this->getService($name);

        $methodLifecycle = ucfirst($method);

        // 1. Before method
        $beforeMethod = "before{$methodLifecycle}";

        if (\method_exists($service, $beforeMethod)) {
            $service->$beforeMethod(...$args);
        }

        // 2. Call the method
        $result = $service->$method(...$args);

        // 3. After method
        $afterMethod = "after{$methodLifecycle}";

        if (\method_exists($service, $afterMethod)) {
            $service->$afterMethod($result, ...$args);
        }

        return $result;
    }

    /**
     * Obtener un servicio
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * Config path
     * 
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }
}
