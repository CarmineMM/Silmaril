<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Foundation\Theme;

class SupportsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \add_action('after_setup_theme', [$this, 'addSupports'], 100);
    }

    public function boot(): void {}

    /**
     * Add theme support
     * 
     * @return void
     */
    public function addSupports(): void
    {
        $supports = $this->theme->config('supports', []);

        foreach ($supports as $feature => $args) {
            if ($args === true) {
                \add_theme_support($feature);
            } else {
                \add_theme_support($feature, $args);
            }
        }
    }
}
