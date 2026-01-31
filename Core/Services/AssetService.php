<?php

namespace Silmaril\Core\Services;

use Silmaril\Core\Foundation\Service;

class AssetService extends Service
{
    public function init(): void
    {
        add_action('wp_enqueue_scripts',  [$this, 'registerFrontendAssets'], 100);
        add_action('admin_enqueue_scripts', [$this, 'registerAdminAssets'], 100);
    }

    /**
     * Register frontend assets
     * 
     * @return void
     */
    public function registerFrontendAssets(): void
    {
        foreach ($this->theme->config('assets.frontend.styles', []) as $handle => $value) {
            \wp_enqueue_style(
                handle: $handle,
                src: $value['src'],
                deps: $value['deps'] ?? [],
                ver: $value['ver'] ?? false,
                media: $value['media'] ?? 'all'
            );
        }

        foreach ($this->theme->config('assets.frontend.scripts', []) as $handle => $value) {
            \wp_enqueue_script(
                handle: $handle,
                src: $value['src'],
                deps: $value['deps'] ?? [],
                ver: $value['ver'] ?? false,
                args: $value['args'] ?? false
            );

            foreach ($this->theme->config("assets.localize.$handle", []) as $key => $value) {
                \wp_localize_script($handle, $key, $value);
            }
        }
    }

    /**
     * Register admin assets
     * 
     * @return void
     */
    public function registerAdminAssets(): void
    {
        foreach ($this->theme->config('assets.admin.styles', []) as $handle => $value) {
            \wp_enqueue_style(
                handle: $handle,
                src: $value['src'],
                deps: $value['deps'] ?? [],
                ver: $value['ver'] ?? false,
                media: $value['media'] ?? 'all'
            );
        }

        foreach ($this->theme->config('assets.admin.scripts', []) as $handle => $value) {
            \wp_enqueue_script(
                handle: $handle,
                src: $value['src'],
                deps: $value['deps'] ?? [],
                ver: $value['ver'] ?? false,
                args: $value['args'] ?? false
            );

            foreach ($this->theme->config("assets.localize.$handle", []) as $key => $value) {
                \wp_localize_script($handle, $key, $value);
            }
        }
    }
}
