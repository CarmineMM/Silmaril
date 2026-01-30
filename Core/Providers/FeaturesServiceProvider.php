<?php

namespace Silmaril\Core\Providers;

use Illuminate\Support\Arr;
use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Services\{FeatureCommentsService, FeatureCategoriesService};

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
    }

    public function boot(): void {}
}
