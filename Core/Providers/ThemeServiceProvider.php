<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Helpers\Filesystem;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \load_theme_textdomain(TEXT_DOMAIN, Filesystem::file($this->theme->config('theme.language_path')));
    }

    public function boot(): void {}
}
