<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Helpers\Filesystem;
use Silmaril\Core\Services\{MenuService, SiteService};

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \load_theme_textdomain(TEXT_DOMAIN, Filesystem::file($this->theme->config('theme.language_path')));

        $this->theme->registerService('site', new SiteService($this->theme));
        $this->theme->registerService('menu', new MenuService($this->theme));
    }

    public function boot(): void {}
}
