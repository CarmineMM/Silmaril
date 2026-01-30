<?php

namespace Silmaril\Core\Providers;

use Illuminate\Support\Arr;
use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Services\{FeatureCommentsService, FeatureCategoriesService, FeatureTagsService};

class FeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Features de comentarios
        if (Arr::some($this->theme->config('theme.features.comments'), fn($value) => (bool) $value)) {
            $this->theme->registerService('feature_comments', new FeatureCommentsService($this->theme));
        }

        // Features de categorias
        if (Arr::some($this->theme->config('theme.features.categories'), fn($value) => (bool) $value)) {
            $this->theme->registerService('feature_categories', new FeatureCategoriesService($this->theme));
        }

        // Features de tags
        if (Arr::some($this->theme->config('theme.features.tags'), fn($value) => (bool) $value)) {
            $this->theme->registerService('feature_tags', new FeatureTagsService($this->theme));
        }

        if ($this->theme->config('theme.features.additional.remove_pingbacks', false)) {
            \add_filter('xmlrpc_methods', [$this, 'disablePingbacks']);
            \add_filter('pre_ping', [$this, 'disablePingbackHeader']);
        }
    }

    public function boot(): void {}

    /**
     * Deshabilitar pingbacks
     */
    public function disablePingbacks(array $methods): array
    {
        unset($methods['pingback.ping']);
        unset($methods['pingback.extensions.getPingbacks']);
        return $methods;
    }

    /**
     * Deshabilitar pingback header
     */
    public function disablePingbackHeader(array &$links): void
    {
        foreach ($links as $key => $link) {
            if (\strpos($link, 'rel="pingback"') !== false) {
                unset($links[$key]);
            }
        }
    }
}
