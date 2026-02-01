<?php

namespace Silmaril\Core\Services;

use Silmaril\Core\Foundation\Service;

class SiteService extends Service
{
    /**
     * Cache transients
     */
    protected const string CACHE_KEY = 'silmaril_site_config';
    protected const string CACHE_GROUP = 'silmaril_theme';

    /**
     * Init
     * 
     * @return void
     */
    public function init(): void {}

    /**
     * Obtener toda la configuración del sitio
     */
    public function getSiteConfig(
        array $config = [
            'ttl' => 3600,
            'basic' => true,
            'branding' => true,
            'seo' => true,
            'contact' => true,
            'permalinks' => true,
            'theme_mods' => true,
        ]
    ): array {
        $cache_enabled = $this->theme->config('cache.enabled', false);
        $constructCacheName = array_keys(
            \array_filter($config, fn($item) => \is_bool($item) && $item)
        );
        $cacheKey = self::CACHE_KEY . ':' . implode('-', $constructCacheName);

        // Intentar obtener de cache
        if ($cache_enabled) {
            $cached = \get_transient($cacheKey);

            if ($cached !== false) {
                return $cached;
            }
        }

        // Construir configuración
        $siteConfig = [
            'timestamp' => time(),
            'cache' => $cache_enabled,
        ];

        if ($config['basic'] ?? false) {
            $siteConfig['basic'] = $this->getBasicInfo();
        }

        if ($config['branding'] ?? false) {
            $siteConfig['branding'] = $this->getBranding();
        }

        if ($config['seo'] ?? false) {
            $siteConfig['seo'] = $this->getSEO();
        }

        if ($config['contact'] ?? false) {
            $siteConfig['contact'] = $this->getContactInfo();
        }

        if ($config['permalinks'] ?? false) {
            $siteConfig['permalinks'] = $this->getPermalinks();
        }

        if ($config['theme_mods'] ?? false) {
            $siteConfig['theme_mods'] = \get_theme_mods();
        }

        // Guardar en cache
        if ($cache_enabled) {
            $ttl = $config['ttl'] ?? 3600; // 1 hora por defecto
            \set_transient($cacheKey, $siteConfig, $ttl);
        }

        return $siteConfig;
    }

    /**
     * Obtener información básica del sitio
     */
    public function getBasicInfo(): array
    {
        return [
            'title' => \get_bloginfo('name'),
            'description' => \get_bloginfo('description'),
            'site_url' => \get_bloginfo('url'),
            'home_url' => \get_bloginfo('wpurl'),
            'admin_email' => \get_bloginfo('admin_email'),
            'charset' => \get_bloginfo('charset'),
            'language' => \get_bloginfo('language'),
            'timezone' => \get_option('timezone_string') ?: 'UTC',
            'date_format' => \get_option('date_format'),
            'time_format' => \get_option('time_format'),
            'start_of_week' => \get_option('start_of_week'),
            'posts_per_page' => \get_option('posts_per_page'),
            'wordpress_version' => \get_bloginfo('version'),
        ];
    }

    /**
     * Obtener información de branding (logo, favicon, etc.)
     */
    public function getBranding(): array
    {
        $custom_logo_id = \get_theme_mod('custom_logo');
        $site_icon_id = \get_option('site_icon');

        return [
            'logo' => [
                'id' => $custom_logo_id,
                'url' => $custom_logo_id ? \wp_get_attachment_image_url($custom_logo_id, 'full') : null,
                'alt' => $custom_logo_id ? \get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true) : '',
                'width' => $custom_logo_id ? \wp_get_attachment_image_src($custom_logo_id, 'full')[1] ?? null : null,
                'height' => $custom_logo_id ? \wp_get_attachment_image_src($custom_logo_id, 'full')[2] ?? null : null,
            ],
            'site_icon' => [
                'id' => $site_icon_id,
                'url' => $site_icon_id ? \wp_get_attachment_image_url($site_icon_id, 'full') : null,
                'favicon_url' => $site_icon_id ? \get_site_icon_url(32) : null,
                'apple_touch_icon' => $site_icon_id ? \get_site_icon_url(180) : null,
            ],
            'custom_logo_id' => $custom_logo_id,
            'header_text' => \get_theme_mod('header_text', true),
            'background_image' => \get_background_image(),
            'background_color' => \get_background_color(),
        ];
    }

    /**
     * Obtener información SEO
     */
    public function getSEO(): array
    {
        // Meta description (puede venir de Customizer o plugin SEO)
        $meta_description = \get_option('silmaril_meta_description') ?: \get_bloginfo('description');

        // Open Graph image
        $og_image_id = \get_option('silmaril_og_image');
        $og_image_url = $og_image_id ? \wp_get_attachment_image_url($og_image_id, 'full') : null;

        // Si no hay og_image personalizado, usar logo o site icon
        if (!$og_image_url) {
            $branding = $this->getBranding();
            $og_image_url = $branding['logo']['url'] ?: $branding['site_icon']['url'];
        }

        return [
            'meta_description' => $meta_description,
            'og_image' => $og_image_url,
            'og_site_name' => \get_bloginfo('name'),
            'twitter_card' => 'summary_large_image',
            'canonical_url' => \get_bloginfo('url'),
            'robots' => 'index, follow',
        ];
    }

    /**
     * Obtener información de contacto
     */
    public function getContactInfo(): array
    {
        return [
            'email' => \get_option('silmaril_email') ?: \get_bloginfo('admin_email'),
        ];
    }

    /**
     * Obtener permalinks
     */
    protected function getPermalinks(): array
    {
        return [
            'structure' => get_option('permalink_structure'),
            'category_base' => get_option('category_base'),
            'tag_base' => get_option('tag_base'),
        ];
    }
}
