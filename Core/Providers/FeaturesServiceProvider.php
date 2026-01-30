<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;
use Silmaril\Core\Services\FeatureCommentsService;

class FeaturesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->theme->registerService('feature_comments', new FeatureCommentsService($this->theme));
    }

    public function boot(): void {}
}
