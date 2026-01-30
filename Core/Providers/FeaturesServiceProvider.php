<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Services\FeaturesService;

class FeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->theme->registerService('features', new FeaturesService($this->theme));
    }

    public function boot(): void {}
}
