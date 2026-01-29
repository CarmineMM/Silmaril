<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;

class FilterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($this->theme->config('filters') as $handle) {
            \add_filter(
                $handle['filter'],
                $handle['callback'],
                $handle['priority'],
                $handle['args']
            );
        }
    }

    public function boot(): void {}
}
