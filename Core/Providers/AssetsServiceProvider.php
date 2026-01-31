<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Services\AssetService;

class AssetsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->theme->registerService('assets', new AssetService($this->theme));
    }

    public function boot(): void {}
}
