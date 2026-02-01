<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Controllers\{MenuController, SiteController};
use Silmaril\Core\Foundation\Bootstrap;
use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Hooks\RestApiHook;

class RestApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (!$this->theme->config('api.enabled', false)) {
            RestApiHook::disabledApiRest();
            return;
        }

        if ($this->theme->config('api.site_config.basic.enabled', false)) {
            \add_action('rest_api_init', [$this, 'registerEndpointsBasic'], 100);
        }

        if ($this->theme->config('api.site_menu.enabled', false)) {
            \add_action('rest_api_init', [$this, 'registerEndpointsMenu'], 110);
        }
    }

    public function boot(): void
    {
        if ($this->theme->config('api.cors.enabled', false)) {
            \add_action(
                hook_name: 'rest_api_init',
                callback: fn() => \add_filter(
                    'rest_pre_serve_request',
                    [RestApiHook::class, 'configureCors'],
                    10,
                    4
                )
            );
        }

        if ($this->theme->config('api.rate_limit.enabled', false)) {
            \add_filter(
                hook_name: 'rest_pre_dispatch',
                callback: [RestApiHook::class, 'checkRateLimit'],
                priority: 10,
                accepted_args: 3
            );
        }
    }

    /**
     * Register endpoint to basic
     */
    public function registerEndpointsBasic(): void
    {
        $namespace = $this->theme->config('api.namespace', 'silmaril/v1');
        $permissionsCallback = $this->theme->config('api.site_config.permission_callback', fn() => true);

        $siteController = Bootstrap::controller(SiteController::class, $this->theme);

        \register_rest_route($namespace, '/site', [
            'methods' => 'GET',
            'callback' => [$siteController, 'getSiteConfig'],
            'permission_callback' => $permissionsCallback,
            'args' => [],
        ]);

        // Endpoint para site config extendida
        \register_rest_route($namespace, '/site/(?P<type>[a-z_]+)', [
            [
                'methods' => 'GET',
                'callback' => [$siteController, 'getSiteConfigByType'],
                'permission_callback' => $permissionsCallback,
                'args' => [
                    'type' => [
                        'description' => 'Tipo de configuración (basic, branding, seo, etc.)',
                        'type' => 'string',
                        'required' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Eegister endpoints menu
     */
    public function registerEndpointsMenu(): void
    {
        $namespace = $this->theme->config('api.namespace', 'silmaril/v1');
        $permissionsCallback = $this->theme->config('api.site_menu.permission_callback', fn() => true);
        $menuController = Bootstrap::controller(MenuController::class, $this->theme);

        register_rest_route($namespace, '/menus', [
            [
                'methods' => 'GET',
                'callback' => [$menuController, 'getMenus'],
                'permission_callback' => $permissionsCallback,
                'args' => [],
            ],
        ]);
    }
}
