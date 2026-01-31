<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;

class TaxonomyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \add_action('init', [$this, 'registerTaxonomies'],  30);
    }

    public function boot(): void {}

    public function registerTaxonomies(): void
    {
        foreach ($this->theme->config('taxonomies', []) as $taxonomy => $values) {
            \register_taxonomy($taxonomy, $values['object_type'], $values['args']);
        }
    }
}
