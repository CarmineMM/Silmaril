<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($this->theme->config('hooks') as $handle) {
            \add_action($handle['hook'], $handle['callback'], $handle['priority'] ?? 10, $handle['args'] ?? 1);
        }
    }

    public function boot(): void {}
}
