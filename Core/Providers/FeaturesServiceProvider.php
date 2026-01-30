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

        // Aditional features
        if ($this->theme->config('theme.features.additional.remove_author_archives', false)) {
            \add_action('template_redirect', [$this, 'disableAuthorArchives']);
        }

        if ($this->theme->config('theme.features.additional.remove_date_archives', false)) {
            \add_action('template_redirect', [$this, 'disableDateArchives']);
        }
    }

    public function boot(): void
    {
        if ($this->theme->config('theme.features.additional.remove_pingbacks', false)) {
            \add_filter('xmlrpc_methods', [$this, 'disablePingbacks']);
            \add_filter('pre_ping', [$this, 'disablePingbackHeader']);
        }

        if ($this->theme->config('theme.features.additional.remove_trackbacks', false)) {
            \add_filter('comments_open', [$this, 'disableTrackbacks'], 100, 2);
            \add_filter('pings_open', '__return_false', 100, 2);
        }
    }

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

    /**
     * Deshabilitar archives de autores
     */
    public function disableAuthorArchives(): void
    {
        if (is_author()) {
            global $wp_query;
            $wp_query->set_404();
            \status_header(404);
            include \get_query_template('404');
            exit;
        }
    }

    /**
     * Deshabilitar archives por fecha
     */
    public function disableDateArchives(): void
    {
        if (is_date() || is_year() || is_month() || is_day()) {
            global $wp_query;
            $wp_query->set_404();
            \status_header(404);
            include \get_query_template('404');
            exit;
        }
    }

    /**
     * Deshabilitar trackbacks
     */
    public function disableTrackbacks(bool $open, int $post_id): bool
    {
        if (\get_post_type($post_id) === 'post') {
            return false;
        }
        return $open;
    }
}
