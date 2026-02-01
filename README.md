# Silmaril - WordPress Theme with Laravel Architecture

> Un tema de WordPress moderno que combina la flexibilidad de WordPress con la arquitectura elegante de Laravel, proporcionando una base sólida y escalable para desarrollo profesional.

## ⚠️ WARNINGS CRÍTICOS

> **NUNCA editar la carpeta `Core/`** - Esta carpeta contiene el framework base y es el corazón del sistema. Cualquier modificación puede romper el funcionamiento general del tema.

> **SIEMPRE trabajar en la carpeta `App/`** - Toda extensión, configuración y código personalizado debe estar en esta carpeta. El sistema está diseñado para mantener `Core/` intacto y actualizabile.

> **NO modificar `functions.php`, `Bootstrap.php`, o archivos de Core/Foundation/** - Estos archivos manejan la inicialización crítica del tema.

> **NO desactivar service providers en producción sin entender sus dependencias** - Algunos providers dependen de otros. Consultar la sección de ciclo de vida.

---

## Tabla de Contenidos

1. [Introducción](#introducción)
2. [Requisitos e Instalación](#requisitos-e-instalación)
3. [Comparativa: WordPress Estándar vs Silmaril](#comparativa-wordpress-estándar-vs-silmaril)
4. [Estructura de Directorios](#estructura-de-directorios)
5. [Ciclo de Vida e Inicialización](#ciclo-de-vida-e-inicialización)
6. [Sistema de Configuración](#sistema-de-configuración)
7. [Service Providers](#service-providers)
8. [Servicios y Service Locator](#servicios-y-service-locator)
9. [Controladores REST API](#controladores-rest-api)
10. [Sistema de Hooks y Filtros](#sistema-de-hooks-y-filtros)
11. [Assets, Caché y Características](#assets-caché-y-características)
12. [Guía de Desarrollo](#guía-de-desarrollo)
13. [Referencia Rápida](#referencia-rápida)

---

## Introducción

**Silmaril** es un tema de WordPress que reimagina el desarrollo tradicional de temas incorporando conceptos y patrones de Laravel 12. En lugar de depender de hooks dispersos y funciones globales, Silmaril organiza la lógica en **Service Providers**, **Servicios**, **Controladores** y **Configuración centralizada**.

### Características Principales

- **Arquitectura basada en Service Providers** (inspirada en Laravel)
- **Configuración centralizada** en la carpeta `App/config/`
- **Inyección de dependencias** mediante Theme singleton
- **Ciclo de vida estructurado** con métodos `register()` y `boot()`
- **API REST nativa** con controladores type-safe
- **Sistema de caché** con generadores automáticos
- **Separación clara** entre framework (`Core/`) y aplicación (`App/`)
- **Soporte para características desactivables** (comments, categories, tags, Gutenberg)

### Requisitos e Instalación

- **PHP 8.3 o superior**
- **WordPress 6.0 o superior**
- **Composer** (para gestionar dependencias)

**Dependencias principales:**
- `illuminate/support` ^12.0 - Utilidades de Laravel (Arrays, Strings, etc.)
- `carminemm/units-conversion` ^1.2 - Conversión de unidades personalizadas
- `symfony/var-dumper` ^7.0 - Debugging (desarrollo)

**Instalación:**

```bash
# 1. Clonar el tema en wp-content/themes/
git clone <repo-url> wp-content/themes/silmaril

# 2. Instalar dependencias
cd wp-content/themes/silmaril
composer install

# 3. Activar el tema desde WordPress o con WP-CLI
wp theme activate silmaril
```

---

## Comparativa: WordPress Estándar vs Silmaril

### WordPress Estándar

En WordPress tradicional, la inicialización y configuración se realiza mediante hooks dispersos:

```php
// ❌ WordPress Estándar - Disperso y difícil de mantener
add_action('after_setup_theme', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list']);
});

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main.css');
    wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main.js', ['jquery']);
});

add_filter('acf/save_post', function() {
    // lógica personalizada
});

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
// ... más hooks esparcidos
```

**Problemas:**
- Código disperso en múltiples archivos
- Difícil de mantener y depurar
- Orden de ejecución no claro
- Reutilización de código limitada

### Silmaril

Silmaril organiza todo en service providers con un ciclo de vida explícito:

```php
// ✅ Silmaril - Organizado y mantenible

// 1. Configuración centralizada (App/config/providers.php)
return [
    'auto_boot' => [
        \Silmaril\Core\Providers\SupportsServiceProvider::class,
        \Silmaril\Core\Providers\ThemeServiceProvider::class,
        \Silmaril\Core\Providers\AssetsServiceProvider::class,
        // ... más providers
    ],
];

// 2. Cada provider tiene ciclo de vida definido
class AssetsServiceProvider extends ServiceProvider {
    public function register() {
        // Registrar servicios en el contenedor
        $this->theme->registerService('assets', new AssetService($this->theme));
    }
    
    public function boot() {
        // Ejecutar cuando WordPress esté listo
        $this->theme->getService('assets')->enqueueAssets();
    }
}

// 3. Acceso centralizado desde templates o servicios
$assetService = theme()->getService('assets');
```

**Ventajas:**
- ✅ Código organizado y modular
- ✅ Ciclo de vida explícito
- ✅ Fácil de testear
- ✅ Reutilización de componentes
- ✅ Separación de responsabilidades

---

## Estructura de Directorios

```
silmaril/
├── App/                              # 📝 ZONA EDITABLE - Configuración del proyecto
│   ├── assets/
│   │   ├── main.css                 # CSS del tema
│   │   └── main.js                  # JavaScript del tema
│   ├── config/
│   │   ├── api.php                  # Configuración REST API
│   │   ├── assets.php               # Encolamiento de CSS/JS
│   │   ├── cache.php                # Sistema de caché
│   │   ├── filters.php              # Filtros personalizados
│   │   ├── hooks.php                # Acciones personalizadas
│   │   ├── post_types.php           # Tipos de contenido custom
│   │   ├── providers.php            # Registro de providers
│   │   ├── supports.php             # Theme support features
│   │   ├── taxonomies.php           # Taxonomías personalizadas
│   │   └── theme.php                # Información del tema
│   ├── Hooks/
│   │   ├── RemoveActionsHook.php    # Eliminar acciones WordPress
│   │   └── RestApiInitHook.php      # Inicialización REST API
│   ├── Providers/                   # 📝 Crear providers personalizados aquí
│   │   └── [YourProvider.php]
│   └── Services/                    # 📝 Crear servicios personalizados aquí
│       └── [YourService.php]
│
├── Core/                             # ⛔ ZONA INTOCABLE - Framework base
│   ├── Contracts/
│   │   ├── ContentInterface.php      # Interface para contenido
│   │   ├── ControllerInterface.php   # Interface para controladores REST
│   │   ├── ServiceInterface.php      # Interface para servicios
│   │   └── ServiceProviderInterface.php
│   ├── Controllers/
│   │   ├── MenuController.php        # REST API para menús
│   │   └── SiteController.php        # REST API para config del sitio
│   ├── Exceptions/
│   │   └── ServiceProviderNotFound.php
│   ├── Filters/
│   │   └── PageContentFilter.php     # Filtro de contenido de páginas
│   ├── Foundation/
│   │   ├── Bootstrap.php             # Entry point del tema
│   │   ├── Theme.php                 # Singleton principal (Theme instance)
│   │   ├── BaseController.php        # Clase base para controladores
│   │   ├── Service.php               # Clase base para servicios
│   │   ├── ServiceProvider.php       # Clase base para providers
│   │   ├── RoadTracer.php            # Debugging y trazado de ejecución
│   │   ├── ContentBootstrap.php      # Bootstrap de contenido
│   │   └── Cache/
│   │       ├── CacheService.php      # Servicio de caché
│   │       ├── CacheGenerator.php    # Generador de caché
│   │       └── ConfigCacheGenerator.php
│   ├── Helpers/
│   │   ├── Filesystem.php            # Utilidades de sistema de archivos
│   │   └── functions.php             # Funciones globales (theme(), etc.)
│   ├── Hooks/
│   │   ├── HtmlContentHook.php       # Hook para agregar meta tags
│   │   └── RestApiHook.php           # Hook para inicializar REST API
│   ├── Providers/                    # Service providers core
│   │   ├── AssetsServiceProvider.php
│   │   ├── EditorServiceProvider.php
│   │   ├── FeaturesServiceProvider.php
│   │   ├── FilterServiceProvider.php
│   │   ├── HookServiceProvider.php
│   │   ├── PostTypeServiceProvider.php
│   │   ├── RestApiServiceProvider.php
│   │   ├── SupportsServiceProvider.php
│   │   ├── TaxonomyServiceProvider.php
│   │   └── ThemeServiceProvider.php
│   └── Services/
│       ├── AssetService.php          # Gestión de assets
│       ├── SiteService.php           # Información del sitio
│       ├── MenuService.php           # Gestión de menús
│       ├── FeatureCommentsService.php
│       ├── FeatureCategoriesService.php
│       └── FeatureTagsService.php
│
├── functions.php                    # ⚠️ NO EDITAR - Entry point del tema
├── header.php                       # Template de encabezado
├── footer.php                       # Template de pie de página
├── index.php                        # Template principal
├── page.php                         # Template de páginas
├── single.php                       # Template de posts individuales
├── 404.php                          # Template de error 404
├── sidebar.php                      # Template de barra lateral
├── style.css                        # Metadatos del tema (nombre, autor, etc.)
├── composer.json                    # Dependencias del proyecto
├── composer.lock
├── LICENSE.txt
├── README.md                        # Este archivo
└── human.txt                        # Metadata (linked en header)
```

### Convención de Directorios

- **`App/`**: Todo el código personalizado del proyecto va aquí
- **`Core/`**: Framework intocable. NO editar nunca
- **`App/config/`**: Configuración centralizada del tema
- **`App/Providers/`**: Tus propios service providers
- **`App/Services/`**: Tus propios servicios de negocio
- **`App/Hooks/`**: Hooks personalizados (se cargan automáticamente)

---

## Ciclo de Vida e Inicialización

El tema sigue un ciclo de vida explícito desde la activación hasta que todo está listo. Entender este flujo es crucial para saber cuándo se ejecuta tu código.

### Secuencia de Carga

```
1. WordPress carga wp-config.php y wp-settings.php
   ↓
2. WordPress busca y carga functions.php del tema (nuestro entry point)
   ↓
3. functions.php carga Composer autoloader e inicia Bootstrap::run()
   ↓
4. Bootstrap::run() registra el hook 'after_setup_theme'
   ↓
5. WordPress dispara 'after_setup_theme' hook
   ↓
6. Bootstrap::init() se ejecuta (callback del hook)
   ↓
7. Se crea Theme singleton y se carga la configuración
   ↓
8. Theme::bootstrap() ejecuta el ciclo de providers:
   
   a) instanceRegisterProviders()
      ↓ Instancia todos los providers
      ↓ Llama register() en cada uno
   
   b) initServices()
      ↓ Llama init() en todos los servicios registrados
   
   c) bootProviders()
      ↓ Llama boot() en cada provider
   
   d) Generación de caché (si está habilitada)

9. WordPress continúa con otros hooks
   ↓
10. Se disparan hooks de REST API (rest_api_init)
    ↓
11. Tema completamente cargado y funcional
```

### Métodos del Ciclo de Vida

Cada provider tiene dos métodos principales:

**`register()`** - Se ejecuta PRIMERO
- Propósito: Registrar servicios en el contenedor
- Cuándo: Al principio de la inicialización
- Uso: `$this->theme->registerService('name', $service)`
- NO ejecutar acciones de WordPress aquí

```php
public function register()
{
    // ✅ Registrar servicios
    $this->theme->registerService('assets', new AssetService($this->theme));
    
    // ❌ NO ejecutar acciones WordPress aquí
}
```

**`boot()`** - Se ejecuta DESPUÉS de `register()`
- Propósito: Ejecutar acciones que necesitan otros servicios
- Cuándo: Después que todos los servicios están registrados
- Uso: Llamar métodos en servicios, enqueuing, agregar hooks
- Aquí SÍ se ejecutan acciones de WordPress

```php
public function boot()
{
    // ✅ Acceder a otros servicios
    $assetService = $this->theme->getService('assets');
    $assetService->enqueueAssets();
    
    // ✅ Agregar acciones/filtros
    add_action('wp_head', [$this, 'handleHeadContent']);
}
```

### Orden de Ejecución de Providers

Los providers se cargan en el orden especificado en `App/config/providers.php`:

```php
'auto_boot' => [
    \Silmaril\Core\Providers\SupportsServiceProvider::class,        // 1
    \Silmaril\Core\Providers\ThemeServiceProvider::class,           // 2
    \Silmaril\Core\Providers\AssetsServiceProvider::class,          // 3
    \Silmaril\Core\Providers\EditorServiceProvider::class,          // 4
    \Silmaril\Core\Providers\TaxonomyServiceProvider::class,        // 5
    \Silmaril\Core\Providers\PostTypeServiceProvider::class,        // 6
    \Silmaril\Core\Providers\FeaturesServiceProvider::class,        // 7
    \Silmaril\Core\Providers\HookServiceProvider::class,            // 8
    \Silmaril\Core\Providers\RestApiServiceProvider::class,         // 9
    \Silmaril\Core\Providers\FilterServiceProvider::class,          // 10
],
```

**Implicaciones:**
- `SupportsServiceProvider` debe ser primero (activa features básicas)
- `ThemeServiceProvider` debe ser segundo (registra servicios principales)
- `TaxonomyServiceProvider` y `PostTypeServiceProvider` deben ir antes de `FeaturesServiceProvider`
- El orden puede afectar dependencias entre providers

---

## Sistema de Configuración

Todo el comportamiento del tema se controla desde archivos de configuración en `App/config/`. Esta centralización facilita el mantenimiento y la documentación.

### Cargas de Configuración

**Automática** (en `after_setup_theme`):
- `theme.php` - Información del tema
- `supports.php` - Theme support features
- `assets.php` - Encolamiento de CSS/JS
- `hooks.php` - Acciones personalizadas
- `filters.php` - Filtros personalizados
- `api.php` - Configuración REST API
- `cache.php` - Sistema de caché
- `providers.php` - Providers a cargar

**Diferida** (después de `after_setup_theme`):
- `post_types.php` - Tipos de contenido custom
- `taxonomies.php` - Taxonomías personalizadas

### App/config/providers.php

Define qué service providers se cargan y en qué orden.

```php
<?php

return [
    'auto_boot' => [
        \Silmaril\Core\Providers\SupportsServiceProvider::class,
        \Silmaril\Core\Providers\ThemeServiceProvider::class,
        \Silmaril\Core\Providers\AssetsServiceProvider::class,
        \Silmaril\Core\Providers\EditorServiceProvider::class,
        \Silmaril\Core\Providers\TaxonomyServiceProvider::class,
        \Silmaril\Core\Providers\PostTypeServiceProvider::class,
        \Silmaril\Core\Providers\FeaturesServiceProvider::class,
        \Silmaril\Core\Providers\HookServiceProvider::class,
        \Silmaril\Core\Providers\RestApiServiceProvider::class,
        \Silmaril\Core\Providers\FilterServiceProvider::class,
        // Agregar tus propios providers aquí:
        // \Silmaril\App\Providers\YourCustomProvider::class,
    ],
];
```

### App/config/supports.php

Activa features estándar de WordPress usando `add_theme_support()`.

```php
<?php

return [
    'title-tag' => true,
    'post-thumbnails' => true,
    'html5' => ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'],
    'custom-logo' => [
        'height' => 100,
        'width' => 100,
        'flex-height' => true,
        'flex-width' => true,
    ],
    'custom-background' => true,
    'responsive-embeds' => true,
    'editor-styles' => true,
    'wp-block-styles' => true,
    'align-wide' => true,
    'automatic-feed-links' => true,
];
```

### App/config/assets.php

Configura el encolamiento de CSS y JavaScript frontend y admin.

```php
<?php

return [
    'frontend' => [
        'css' => [
            'main' => [
                'path' => '/assets/main.css',
                'deps' => [],
                'version' => null,
                'media' => 'all',
            ],
        ],
        'js' => [
            'main' => [
                'path' => '/assets/main.js',
                'deps' => ['jquery'],
                'version' => null,
                'in_footer' => true,
                'localize' => [
                    'handle' => 'main',
                    'object_name' => 'ThemeData',
                    'l10n_data' => [
                        'ajaxUrl' => admin_url('admin-ajax.php'),
                        'homeUrl' => home_url(),
                    ],
                ],
            ],
        ],
    ],
    'admin' => [
        'css' => [],
        'js' => [],
    ],
];
```

### App/config/hooks.php

Define acciones personalizadas que se registran automáticamente.

```php
<?php

return [
    // Formato: 'hook_name' => ['class', 'method'] o callable
    'wp_head' => [
        ['priority' => 1, 'callback' => \Silmaril\Core\Hooks\HtmlContentHook::class . '@addHeadContent'],
    ],
    'init' => [
        ['priority' => 10, 'callback' => \Silmaril\App\Hooks\RemoveActionsHook::class . '@initActions'],
    ],
    'rest_api_init' => [
        ['priority' => 10, 'callback' => \Silmaril\App\Hooks\RestApiInitHook::class . '@getFeatureMedia'],
    ],
];
```

### App/config/filters.php

Define filtros personalizados que se registran automáticamente.

```php
<?php

return [
    // Formato: 'filter_name' => ['class', 'method'] o callable
    // Ejemplo:
    'the_content' => [
        ['priority' => 10, 'callback' => function($content) {
            return $content;
        }],
    ],
];
```

### App/config/post_types.php

Define tipos de contenido personalizados.

```php
<?php

return [
    'portfolio' => [
        'label' => 'Portafolio',
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'portfolio'],
    ],
];
```

### App/config/taxonomies.php

Define taxonomías personalizadas.

```php
<?php

return [
    'portfolio_category' => [
        'label' => 'Categoría del Portafolio',
        'object_type' => ['portfolio'],
        'public' => true,
        'hierarchical' => true,
    ],
];
```

### App/config/api.php

Configura la REST API del tema.

```php
<?php

return [
    'enabled' => false,
    'namespace' => 'silmaril/v1',
    'cors' => [
        'enabled' => false,
        'allowed_origins' => ['*'],
    ],
    'rate_limit' => [
        'enabled' => false,
        'limit' => 100,
        'window' => 3600,
    ],
];
```

### App/config/cache.php

Configura el sistema de caché.

```php
<?php

return [
    'enabled' => !WP_DEBUG,
    'path' => get_template_directory() . '/bootstrap/cache/',
    'lifetime' => null,
    'key' => 'silmaril-v' . wp_get_theme()->get('Version'),
    'components' => ['config', 'providers', 'services'],
    'auto_regenerate' => false,
];
```

---

## Service Providers

Los **Service Providers** son clases que organizan la lógica de inicialización del tema. Cada provider es responsable de registrar y configurar un conjunto de funcionalidades relacionadas.

### Concepto

Un Service Provider:
1. **Registra servicios** en el contenedor (método `register()`)
2. **Los inicializa** después que todos están registrados (método `boot()`)
3. Se carga en un orden específico definido en `providers.php`

### Estructura Base

```php
<?php

namespace Silmaril\App\Providers;

use Silmaril\Core\Foundation\ServiceProvider;

class YourCustomProvider extends ServiceProvider
{
    /**
     * Registrar servicios en el contenedor
     */
    public function register()
    {
        // Registrar un servicio
        $this->theme->registerService('your_service', new YourService($this->theme));
    }

    /**
     * Inicializar providers después que todos están registrados
     */
    public function boot()
    {
        // Acceder a otros servicios
        $service = $this->theme->getService('your_service');
        
        // Ejecutar lógica que depende de otros servicios
        add_action('wp_head', [$this, 'doSomething']);
    }

    public function doSomething()
    {
        // Lógica aquí
    }
}
```

### Providers Core - Qué hacen

#### 1. SupportsServiceProvider
Activa features de WordPress mediante `add_theme_support()`. Debe ser el PRIMERO.

```php
// Archivo: Core/Providers/SupportsServiceProvider.php
public function boot()
{
    $supports = config('supports');
    
    foreach ($supports as $feature => $args) {
        if ($args === true) {
            add_theme_support($feature);
        } else {
            add_theme_support($feature, $args);
        }
    }
}
```

#### 2. ThemeServiceProvider
Registra servicios principales (Site, Menu) y carga el text domain.

```php
public function register()
{
    $this->theme->registerService('site', new SiteService($this->theme));
    $this->theme->registerService('menu', new MenuService($this->theme));
}

public function boot()
{
    load_theme_textdomain('silmaril', get_template_directory() . '/languages');
}
```

#### 3. AssetsServiceProvider
Registra el `AssetService` que maneja encolamiento de CSS/JS.

```php
public function register()
{
    $this->theme->registerService('assets', new AssetService($this->theme));
}

public function boot()
{
    $this->theme->getService('assets')->enqueueAssets();
}
```

#### 4. EditorServiceProvider
Desactiva Gutenberg si está configurado en `config/features.php`.

```php
public function boot()
{
    if (config('features.gutenberg.disable')) {
        add_filter('use_block_editor_for_post_type', '__return_false');
    }
}
```

#### 5. TaxonomyServiceProvider
Registra taxonomías definidas en `config/taxonomies.php`.

```php
public function boot()
{
    $taxonomies = config('taxonomies', []);
    foreach ($taxonomies as $taxonomy => $args) {
        register_taxonomy($taxonomy, $args['object_type'], $args);
    }
}
```

#### 6. PostTypeServiceProvider
Registra tipos de contenido definidos en `config/post_types.php`.

```php
public function boot()
{
    $postTypes = config('post_types', []);
    foreach ($postTypes as $postType => $args) {
        register_post_type($postType, $args);
    }
}
```

#### 7. FeaturesServiceProvider
Gestiona features desactivables (comments, categories, tags).

```php
public function boot()
{
    if (config('features.comments.disable')) {
        // Remover comments completamente
        remove_post_type_support('post', 'comments');
        remove_menu_page('edit-comments.php');
    }
}
```

#### 8. HookServiceProvider
Registra acciones personalizadas desde `config/hooks.php`.

```php
public function boot()
{
    $hooks = config('hooks', []);
    foreach ($hooks as $hookName => $callbacks) {
        foreach ($callbacks as $hook) {
            add_action($hookName, $hook['callback'], $hook['priority']);
        }
    }
}
```

#### 9. RestApiServiceProvider
Inicializa los controladores REST API si está habilitado en `config/api.php`.

```php
public function boot()
{
    if (!config('api.enabled')) return;
    
    $controllers = [
        new SiteController($this->theme),
        new MenuController($this->theme),
    ];
    
    foreach ($controllers as $controller) {
        $controller->init();
    }
}
```

#### 10. FilterServiceProvider
Registra filtros personalizados desde `config/filters.php`.

```php
public function boot()
{
    $filters = config('filters', []);
    foreach ($filters as $filterName => $callbacks) {
        foreach ($callbacks as $filter) {
            add_filter($filterName, $filter['callback'], $filter['priority']);
        }
    }
}
```

### Crear un Provider Personalizado

1. Crear archivo en `App/Providers/YourProvider.php`:

```php
<?php

namespace Silmaril\App\Providers;

use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\App\Services\YourService;

class YourProvider extends ServiceProvider
{
    public function register()
    {
        $this->theme->registerService('your_service', new YourService($this->theme));
    }

    public function boot()
    {
        $service = $this->theme->getService('your_service');
        $service->initialize();
    }
}
```

2. Registrarlo en `App/config/providers.php`:

```php
'auto_boot' => [
    // ... providers existentes
    \Silmaril\App\Providers\YourProvider::class,
],
```

---

## Servicios y Service Locator

Los **Servicios** contienen la lógica de negocio del tema. Cada servicio es responsable de una funcionalidad específica. Se acceden a través del Theme singleton usando el patrón **Service Locator**.

### Concepto de Servicio

Un Servicio es una clase que encapsula funcionalidad relacionada:

```php
<?php

namespace Silmaril\App\Services;

use Silmaril\Core\Foundation\Service;

class YourService extends Service
{
    public function initialize()
    {
        // Inicialización después del boot de providers
    }

    public function doSomething()
    {
        return 'resultado';
    }
}
```

### Acceder a Servicios

Existen varias formas de acceder a servicios registrados:

```php
// Forma 1: Obtener la instancia del servicio
$service = theme()->getService('your_service');
$result = $service->doSomething();

// Forma 2: Llamar método directamente sin obtener instancia
$result = theme()->callServiceMethod('your_service', 'doSomething');

// Forma 3: Con argumentos
$result = theme()->callServiceMethod('your_service', 'methodName', $arg1, $arg2);
```

### Servicios Core

#### 1. AssetService
Gestiona el encolamiento de CSS y JavaScript.

```php
namespace Silmaril\Core\Services;

class AssetService extends Service
{
    public function enqueueAssets()
    {
        $assets = config('assets');
        
        // Frontend CSS
        foreach ($assets['frontend']['css'] ?? [] as $handle => $css) {
            wp_enqueue_style($handle, $css['path'], $css['deps'], $css['version']);
        }
        
        // Frontend JS
        foreach ($assets['frontend']['js'] ?? [] as $handle => $js) {
            wp_enqueue_script($handle, $js['path'], $js['deps'], $js['version'], $js['in_footer']);
            
            if (isset($js['localize'])) {
                wp_localize_script($js['localize']['handle'], $js['localize']['object_name'], $js['localize']['l10n_data']);
            }
        }
    }
}
```

**Métodos principales:**
- `enqueueAssets()` - Encolar assets frontend y admin

#### 2. SiteService
Obtiene información de configuración del sitio con caché en transientes.

```php
namespace Silmaril\Core\Services;

class SiteService extends Service
{
    public function getBasicInfo()
    {
        return [
            'title' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url(),
            'language' => get_bloginfo('language'),
        ];
    }

    public function getBranding()
    {
        return [
            'logo_url' => get_custom_logo(),
            'favicon' => get_site_icon_url(),
        ];
    }

    public function getSEO()
    {
        return [
            'tagline' => get_bloginfo('description'),
        ];
    }
}
```

**Métodos principales:**
- `getBasicInfo()` - Información básica del sitio
- `getBranding()` - Datos de branding
- `getSEO()` - Datos para SEO
- `getContact()` - Información de contacto
- `getPermalinks()` - Estructura de URLs
- `getThemeMods()` - Customizer settings

#### 3. MenuService
Gestiona menús de WordPress.

```php
namespace Silmaril\Core\Services;

class MenuService extends Service
{
    public function getAllMenus()
    {
        $menus = get_terms('nav_menu', ['hide_empty' => true]);
        return array_map(function($menu) {
            return [
                'id' => $menu->term_id,
                'name' => $menu->name,
                'slug' => $menu->slug,
            ];
        }, $menus);
    }

    public function getMenuByLocation($location)
    {
        $menu_id = get_nav_menu_locations()[$location] ?? null;
        if (!$menu_id) return null;
        
        $items = wp_get_nav_menu_items($menu_id);
        return $this->buildMenuTree($items);
    }

    private function buildMenuTree($items, $parent_id = 0)
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item->menu_item_parent == $parent_id) {
                $tree[] = [
                    'id' => $item->ID,
                    'title' => $item->title,
                    'url' => $item->url,
                    'children' => $this->buildMenuTree($items, $item->ID),
                ];
            }
        }
        return $tree;
    }
}
```

**Métodos principales:**
- `getAllMenus()` - Obtener todos los menús registrados
- `getMenuByLocation($location)` - Obtener menú por location
- `getMenuById($menu_id)` - Obtener menú por ID

#### 4. Feature Services
Servicios para gestionar features desactivables.

```php
// FeatureCommentsService, FeatureCategoriesService, FeatureTagsService
class FeatureCommentsService extends Service
{
    public function disable()
    {
        remove_post_type_support('post', 'comments');
        remove_post_type_support('page', 'comments');
        // ... más desactivaciones
    }
}
```

### Crear un Servicio Personalizado

1. Crear archivo en `App/Services/YourService.php`:

```php
<?php

namespace Silmaril\App\Services;

use Silmaril\Core\Foundation\Service;

class YourService extends Service
{
    public function initialize()
    {
        // Lógica de inicialización
    }

    public function getData()
    {
        // Retornar datos
        return ['key' => 'value'];
    }

    public function processData($input)
    {
        // Procesar data
        return strtoupper($input);
    }
}
```

2. Registrarlo en un Provider:

```php
public function register()
{
    $this->theme->registerService('your_service', new YourService($this->theme));
}
```

3. Usarlo desde templates o servicios:

```php
// En templates (header.php, single.php, etc.)
$service = theme()->getService('your_service');
$data = $service->getData();

// Acceso directo sin obtener instancia
$result = theme()->callServiceMethod('your_service', 'processData', 'hello');
```

---

## Controladores REST API

Los **Controladores** manejan endpoints REST API. Cada controlador es responsable de una colección de recursos.

### Estructura Base

```php
<?php

namespace Silmaril\App\Controllers;

use Silmaril\Core\Foundation\BaseController;

class YourController extends BaseController
{
    public function init()
    {
        register_rest_route(
            config('api.namespace'),
            '/your-resource',
            [
                'methods' => 'GET',
                'callback' => [$this, 'getResource'],
                'permission_callback' => [$this, 'checkPermission'],
            ]
        );
    }

    public function getResource($request)
    {
        // Lógica para obtener recurso
        return rest_ensure_response(['data' => 'aquí']);
    }

    public function checkPermission($request)
    {
        return true; // o verificar permisos
    }
}
```

### Controladores Core

#### 1. SiteController
Expone endpoints para información de configuración del sitio.

```php
namespace Silmaril\Core\Controllers;

class SiteController extends BaseController
{
    public function init()
    {
        // GET /silmaril/v1/site
        register_rest_route(config('api.namespace'), '/site', [
            'methods' => 'GET',
            'callback' => [$this, 'getSite'],
            'permission_callback' => [$this, 'checkPermission'],
        ]);

        // GET /silmaril/v1/site/{type}
        register_rest_route(config('api.namespace'), '/site/(?P<type>\w+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getSiteInfo'],
            'permission_callback' => [$this, 'checkPermission'],
        ]);
    }

    public function getSite()
    {
        $siteService = $this->theme->getService('site');
        
        return rest_ensure_response([
            'basic' => $siteService->getBasicInfo(),
            'branding' => $siteService->getBranding(),
            'seo' => $siteService->getSEO(),
        ]);
    }

    public function getSiteInfo($request)
    {
        $type = $request['type'];
        $siteService = $this->theme->getService('site');
        
        $method = 'get' . ucfirst($type);
        
        if (!method_exists($siteService, $method)) {
            return new \WP_Error('invalid_type', 'Tipo de información no válido', ['status' => 404]);
        }
        
        return rest_ensure_response($siteService->$method());
    }

    public function checkPermission()
    {
        return true;
    }
}
```

**Endpoints:**
- `GET /silmaril/v1/site` - Toda la información del sitio
- `GET /silmaril/v1/site/basic` - Información básica
- `GET /silmaril/v1/site/branding` - Datos de branding
- `GET /silmaril/v1/site/seo` - Datos para SEO

#### 2. MenuController
Expone endpoints para menús de WordPress.

```php
namespace Silmaril\Core\Controllers;

class MenuController extends BaseController
{
    public function init()
    {
        // GET /silmaril/v1/menus
        register_rest_route(config('api.namespace'), '/menus', [
            'methods' => 'GET',
            'callback' => [$this, 'getMenus'],
            'permission_callback' => [$this, 'checkPermission'],
        ]);

        // GET /silmaril/v1/menus/{location}
        register_rest_route(config('api.namespace'), '/menus/(?P<location>\w+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getMenuByLocation'],
            'permission_callback' => [$this, 'checkPermission'],
        ]);
    }

    public function getMenus()
    {
        $menuService = $this->theme->getService('menu');
        return rest_ensure_response($menuService->getAllMenus());
    }

    public function getMenuByLocation($request)
    {
        $location = $request['location'];
        $menuService = $this->theme->getService('menu');
        
        $menu = $menuService->getMenuByLocation($location);
        
        if (!$menu) {
            return new \WP_Error('menu_not_found', 'Menú no encontrado', ['status' => 404]);
        }
        
        return rest_ensure_response($menu);
    }

    public function checkPermission()
    {
        return true;
    }
}
```

**Endpoints:**
- `GET /silmaril/v1/menus` - Todos los menús disponibles
- `GET /silmaril/v1/menus/{location}` - Menú por location (ej: `primary`)

### Crear un Controlador Personalizado

1. Crear archivo en `App/Controllers/YourController.php`:

```php
<?php

namespace Silmaril\App\Controllers;

use Silmaril\Core\Foundation\BaseController;

class YourController extends BaseController
{
    public function init()
    {
        register_rest_route(
            config('api.namespace'),
            '/your-endpoint',
            [
                'methods' => 'GET',
                'callback' => [$this, 'handle'],
                'permission_callback' => [$this, 'checkPermission'],
                'args' => [
                    'id' => [
                        'type' => 'integer',
                        'required' => true,
                    ],
                ],
            ]
        );
    }

    public function handle($request)
    {
        $id = $request['id'];
        $service = $this->theme->getService('your_service');
        $result = $service->getData($id);
        
        return rest_ensure_response(['data' => $result]);
    }

    public function checkPermission($request)
    {
        // Verificar permisos
        return current_user_can('read');
    }
}
```

2. Registrarlo en `RestApiServiceProvider` personalizado o en `App/config/api.php`:

```php
// Hacerlo en un custom provider o en el boot de RestApiServiceProvider
$controller = new \Silmaril\App\Controllers\YourController($this->theme);
$controller->init();
```

---

## Sistema de Hooks y Filtros

### Diferencia: Hooks en Silmaril vs WordPress Estándar

En WordPress tradicional, los hooks se esparcen por el código:

```php
// ❌ WordPress Estándar
add_action('wp_head', 'my_function', 10, 0);
add_filter('the_title', 'filter_title', 10, 2);
add_action('init', 'remove_stuff', 0);
```

En Silmaril, los hooks se centralizan en configuración:

```php
// ✅ Silmaril (App/config/hooks.php)
return [
    'wp_head' => [
        ['priority' => 1, 'callback' => \Silmaril\Core\Hooks\HtmlContentHook::class . '@addHeadContent'],
    ],
];
```

### Hooks Registrados Automáticamente

Los hooks definidos en `App/config/hooks.php` se registran automáticamente por `HookServiceProvider`:

```php
<?php
// App/config/hooks.php

return [
    'wp_head' => [
        ['priority' => 1, 'callback' => \Silmaril\Core\Hooks\HtmlContentHook::class . '@addHeadContent'],
    ],
    'init' => [
        ['priority' => 10, 'callback' => \Silmaril\App\Hooks\RemoveActionsHook::class . '@initActions'],
    ],
    'rest_api_init' => [
        ['priority' => 10, 'callback' => \Silmaril\App\Hooks\RestApiInitHook::class . '@getFeatureMedia'],
    ],
];
```

### Acciones WordPress Removidas (Seguridad y Rendimiento)

El archivo `App/Hooks/RemoveActionsHook.php` elimina acciones innecesarias:

```php
<?php

namespace Silmaril\App\Hooks;

class RemoveActionsHook
{
    public static function initActions()
    {
        // Seguridad: Ocultar versión de WordPress
        remove_action('wp_head', 'wp_generator');

        // Seguridad: Remover discovery links obsoletos
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');

        // SEO: Remover rel links de navegación
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link');
        remove_action('wp_head', 'start_post_rel_link');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

        // Rendimiento: Remover feeds RSS (configurable)
        if (config('features.remove_feeds')) {
            remove_action('wp_head', 'feed_links_extra', 3);
            remove_action('wp_head', 'feed_links', 2);
        }

        // Rendimiento: Remover emojis (~5KB)
        if (config('features.remove_emoji')) {
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('wp_head', 'print_emoji_detection_script', 7);
        }
    }
}
```

### Agregar Hooks Personalizados

Para agregar un hook personalizado:

1. Crear archivo en `App/Hooks/YourHook.php`:

```php
<?php

namespace Silmaril\App\Hooks;

class YourHook
{
    public static function doSomething()
    {
        // Lógica del hook
        echo 'Hook ejecutado';
    }

    public function doSomethingElse($param)
    {
        // Usar $this si necesitas acceso a instancia
        return $param . ' procesado';
    }
}
```

2. Registrarlo en `App/config/hooks.php`:

```php
return [
    'your_custom_hook' => [
        ['priority' => 10, 'callback' => \Silmaril\App\Hooks\YourHook::class . '@doSomething'],
    ],
    'the_content' => [
        ['priority' => 20, 'callback' => \Silmaril\App\Hooks\YourHook::class . '@doSomethingElse'],
    ],
];
```

### Filtros Personalizados

Los filtros funcionan igual que los hooks. Se definen en `App/config/filters.php`:

```php
<?php
// App/config/filters.php

return [
    'the_title' => [
        ['priority' => 10, 'callback' => function($title) {
            return strtoupper($title);
        }],
    ],
    'the_content' => [
        ['priority' => 20, 'callback' => \Silmaril\Core\Filters\PageContentFilter::class . '@filter'],
    ],
];
```

### Crear Clase de Filtro

```php
<?php

namespace Silmaril\App\Filters;

class CustomFilter
{
    public static function filterContent($content)
    {
        // Procesar contenido
        return $content;
    }

    public static function filterTitle($title)
    {
        // Procesar título
        return $title;
    }
}
```

Registrar en `App/config/filters.php`:

```php
return [
    'the_content' => [
        ['priority' => 10, 'callback' => \Silmaril\App\Filters\CustomFilter::class . '@filterContent'],
    ],
    'the_title' => [
        ['priority' => 10, 'callback' => \Silmaril\App\Filters\CustomFilter::class . '@filterTitle'],
    ],
];
```

---

## Assets, Caché y Características

### Sistema de Assets

El `AssetService` gestiona el encolamiento de CSS y JavaScript desde `App/config/assets.php`.

**Estructura de configuración:**

```php
<?php
// App/config/assets.php

return [
    'frontend' => [
        'css' => [
            'handle' => [
                'path' => '/assets/main.css',
                'deps' => [],
                'version' => null,
                'media' => 'all',
            ],
        ],
        'js' => [
            'handle' => [
                'path' => '/assets/main.js',
                'deps' => ['jquery'],
                'version' => null,
                'in_footer' => true,
                'localize' => [
                    'handle' => 'handle',
                    'object_name' => 'ObjectName',
                    'l10n_data' => [/* datos PHP a JS */],
                ],
            ],
        ],
    ],
    'admin' => [
        'css' => [],
        'js' => [],
    ],
];
```

**Acceso en templates:**

```php
// En header.php, footer.php o hooks
$assetService = theme()->getService('assets');
$assetService->enqueueAssets();

// O acceso directo
wp_enqueue_script('main');
wp_enqueue_style('main');
```

### Sistema de Caché

El sistema de caché genera y almacena cachés de configuración, providers y servicios.

**Configuración en `App/config/cache.php`:**

```php
<?php

return [
    'enabled' => !WP_DEBUG,           // Deshabilitar en debug
    'path' => get_template_directory() . '/bootstrap/cache/',
    'lifetime' => null,                // null = nunca expira, int = segundos
    'key' => 'silmaril-v1.0.0',       // Cambiar para invalidar caché
    'components' => [
        'config',                      // Configuración
        'providers',                   // Providers
        'services',                    // Servicios
        'post_types',                  // Tipos de contenido
        'taxonomies',                  // Taxonomías
    ],
    'auto_regenerate' => false,        // Regenerar automáticamente
];
```

**Cómo funciona:**
1. Si `enabled` es `true` y `WP_DEBUG` es `false`, genera cachés
2. Almacena en base de datos con key `{tema}_{version}_cache_path`
3. Almacena en carpeta `bootstrap/cache/` si está disponible
4. Si `auto_regenerate` es `true`, se regenera en cada cambio
5. Para invalidar: cambiar el `key` en config

**Invalidar caché manualmente:**

```php
// En functions.php o un hook personalizado
delete_option('silmaril_1.0.0_cache_path');
// Luego borrar archivos en bootstrap/cache/ si existen
```

### Sistema de Características (Features)

Las características controlables se configuran en `App/config/features.php` (implícito en `FeaturesServiceProvider`):

**Características disponibles:**

```php
<?php
// Controladas automáticamente por FeaturesServiceProvider

return [
    'comments' => [
        'disable' => true,  // Desactivar comentarios completamente
    ],
    'categories' => [
        'disable' => true,  // Desactivar categorías
    ],
    'tags' => [
        'disable' => true,  // Desactivar etiquetas
    ],
    'gutenberg' => [
        'disable' => false, // Desactivar editor de bloques
    ],
    'remove_feeds' => false,    // Remover RSS feeds
    'remove_emoji' => false,    // Remover emojis
];
```

**Implementación en FeaturesServiceProvider:**

```php
public function boot()
{
    if (config('features.comments.disable')) {
        remove_post_type_support('post', 'comments');
        remove_post_type_support('page', 'comments');
        remove_menu_page('edit-comments.php');
        // ... más desactivaciones
    }

    if (config('features.categories.disable')) {
        register_taxonomy('category', []);
        remove_menu_page('edit-tags.php?taxonomy=category');
    }

    if (config('features.gutenberg.disable')) {
        add_filter('use_block_editor_for_post_type', '__return_false');
        add_filter('use_widgets_block_editor', '__return_false');
    }
}
```

---

## Guía de Desarrollo

### Patrón de Arquitectura

Silmaril utiliza varios patrones de diseño:

#### 1. Singleton Pattern
El Theme es un singleton que centraliza acceso a servicios:

```php
// theme() retorna siempre la misma instancia
$theme = theme();
$theme2 = theme();
// $theme === $theme2 (misma instancia)
```

#### 2. Service Provider Pattern
Cada provider maneja un aspecto de la inicialización:

```php
class YourProvider extends ServiceProvider
{
    public function register()  { /* registrar servicios */ }
    public function boot()      { /* inicializar */ }
}
```

#### 3. Service Locator Pattern
Los servicios se acceden a través del Theme:

```php
$service = theme()->getService('service_name');
```

#### 4. Dependency Injection
Las dependencias se inyectan en constructores:

```php
class YourService extends Service
{
    private $theme;
    
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }
}
```

### Helpers Globales

Funciones disponibles en todo el tema:

```php
// Obtener instancia del Theme
$theme = theme();

// Acceder a servicios
$service = theme()->getService('service_name');
$result = theme()->callServiceMethod('service_name', 'method', $arg1, $arg2);

// Verificar features desactivadas
$comments_disabled = comments_disabled();
$categories_disabled = categories_disabled();
$tags_disabled = tags_disabled();

// Debugging
$tracer = roadTracer();
$tracer->trace('event_name', 'description');
```

### Utilidades de Illuminate/Support

Se incluye `illuminate/support` para utilities de Laravel:

```php
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

// Arrays
$array = ['name' => 'John', 'age' => 30];
Arr::get($array, 'name');           // 'John'
Arr::set($array, 'email', 'j@x.com');
Arr::only($array, ['name']);
Arr::except($array, ['age']);

// Strings
Str::slug('Hello World');           // 'hello-world'
Str::camel('hello_world');          // 'helloWorld'
Str::snake('HelloWorld');           // 'hello_world'
Str::startsWith('hello', 'he');     // true
Str::contains('hello', 'ell');      // true
```

### Debugging con RoadTracer

`RoadTracer` registra la ejecución del tema para debugging:

```php
if (WP_DEBUG) {
    $tracer = roadTracer();
    
    // Registrar evento
    $tracer->trace('my_event', 'descripción');
    
    // Registrar método
    $tracer->traceMethod('Class', 'method');
    
    // Obtener trazas
    $traces = $tracer->getTraces();
}
```

### Estructura de Tipos (PHP 8.3)

Silmaril usa type hints y property types de PHP 8.3:

```php
<?php

namespace Silmaril\App\Services;

use Silmaril\Core\Foundation\Service;

class TypedService extends Service
{
    private array $config = [];
    private int $counter = 0;

    public function __construct(private readonly Theme $theme)
    {
        // Promoción de propiedades en constructor
    }

    public function processData(array $data): string
    {
        // Type hints en parámetros y retorno
        return json_encode($data);
    }

    public function getCount(): int
    {
        return $this->counter;
    }
}
```

### Configuración Helper

Acceder a configuración desde cualquier lugar:

```php
// Obtener valor de config
$value = config('theme.name');
$value = config('assets.frontend.css.main.path');

// Con default
$value = config('custom.key', 'default value');

// Obtener toda la sección
$all_assets = config('assets');
```

---

## Referencia Rápida

### URLs de REST API

```
GET  /wp-json/silmaril/v1/site
GET  /wp-json/silmaril/v1/site/basic
GET  /wp-json/silmaril/v1/site/branding
GET  /wp-json/silmaril/v1/site/seo
GET  /wp-json/silmaril/v1/menus
GET  /wp-json/silmaril/v1/menus/{location}
```

### Métodos Principales del Theme

```php
$theme = theme();

// Servicios
$theme->registerService($name, $service);
$theme->getService($name);
$theme->callServiceMethod($name, $method, ...$args);
$theme->hasService($name);

// Configuración
config($key, $default = null);

// Información
$theme->getName();
$theme->getVersion();
$theme->getTextDomain();
```

### Helpers de Strings (Illuminate)

```php
use Illuminate\Support\Str;

Str::slug('Hello World');           // hello-world
Str::camel('hello_world');          // helloWorld
Str::snake('HelloWorld');           // hello_world
Str::studly('hello_world');         // HelloWorld
Str::plural('post');                // posts
Str::singular('posts');             // post
Str::startsWith($string, $prefix);
Str::endsWith($string, $suffix);
Str::contains($string, $needle);
Str::replace($search, $replace, $subject);
```

### Helpers de Arrays (Illuminate)

```php
use Illuminate\Support\Arr;

Arr::get($array, 'key.nested', 'default');
Arr::set($array, 'key', 'value');
Arr::has($array, 'key');
Arr::only($array, ['key1', 'key2']);
Arr::except($array, ['key1', 'key2']);
Arr::map($array, function($value, $key) {});
Arr::merge($array1, $array2);
```

### Ciclo de Vida de Providers

```
Theme::__construct()
  ↓
theme->instanceRegisterProviders()
  ↓ Llama register() en cada provider
  ↓
theme->initServices()
  ↓ Llama init() en cada servicio
  ↓
theme->bootProviders()
  ↓ Llama boot() en cada provider
  ↓
Tema completamente inicializado
```

### Directorios Clave

| Directorio | Propósito | Editable |
|-----------|----------|----------|
| `App/config/` | Configuración centralizada | ✅ |
| `App/Providers/` | Service providers personalizados | ✅ |
| `App/Services/` | Servicios de negocio | ✅ |
| `App/Hooks/` | Hooks personalizados | ✅ |
| `App/Controllers/` | Controladores REST API | ✅ |
| `Core/` | Framework base | ❌ |
| `Core/Foundation/` | Inicialización del tema | ❌ |
| `Core/Providers/` | Providers core | ❌ |

### Troubleshooting Común

**P: Mi hook/filtro no se ejecuta**
- A: Verificar que esté registrado en `App/config/hooks.php` o `App/config/filters.php`
- A: Revisar que el callback sea válido: `'ClassName@method'` o callable
- A: Confirmar que el provider que lo registra está en `App/config/providers.php`

**P: El servicio no se encuentra**
- A: Registrarlo en el método `register()` del provider: `$this->theme->registerService(...)`
- A: Asegurar que el provider esté en `auto_boot` en `providers.php`
- A: Usar `theme()->getService('nombre_exacto')`

**P: Cambios en configuración no se aplican**
- A: Limpiar caché: cambiar `key` en `App/config/cache.php`
- A: O desactivar caché temporalmente: `'enabled' => false`

**P: No puedo modificar comportamiento de Core**
- A: Crear un Provider personalizado en `App/Providers/`
- A: Agregar hooks/filtros en `App/config/hooks.php` o `App/config/filters.php`
- A: Nunca editar archivos en la carpeta `Core/`

---

## Conclusión

Silmaril proporciona una arquitectura moderna, mantenible y escalable para desarrollo de temas WordPress. Al separar claramente entre la zona editable (`App/`) y el framework (`Core/`), permite:

✅ Código organizado y modular
✅ Fácil mantenimiento y actualización
✅ Reutilización de componentes
✅ Ciclo de vida explícito y predecible
✅ Separación de responsabilidades
✅ Configuración centralizada

Recuerda siempre:
- **Trabajar en `App/`**
- **Nunca tocar `Core/`**
- **Usar configuración centralizada**
- **Mantener servicios pequeños y enfocados**
