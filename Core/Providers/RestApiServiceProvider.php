<?php

namespace Silmaril\Core\Providers;

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
}
