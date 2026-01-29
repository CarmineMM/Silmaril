<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Foundation\Theme;

class SupportsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \add_action('after_setup_theme', [self::class, 'addSupports']);
    }

    public function boot(): void {}

    /**
     * Add theme support
     * 
     * @return void
     */
    public static function addSupports(): void
    {
        $supports = Theme::getInstance()->config('supports', []);

        foreach ($supports as $feature => $args) {
            if ($args === true) {
                add_theme_support($feature);
            } else {
                add_theme_support($feature, $args);
            }
        }
    }
}
