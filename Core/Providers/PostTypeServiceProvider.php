<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;

class PostTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \add_action('init', [$this, 'registerPostTypes'],  40);
    }

    public function boot(): void {}

    /**
     * Register de post types
     *
     * @return void
     */
    public function registerPostTypes(): void
    {
        foreach ($this->theme->config('post_types', []) as $postType => $values) {
            register_post_type($postType, $values);
        }
    }
}
