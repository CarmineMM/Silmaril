<?php

namespace Silmaril\Core\Hooks;

use Silmaril\Core\Foundation\Theme;

class HtmlContentHook
{
    /**
     * Hook para el wp_head
     * 
     * @return void
     */
    public static function addHeadContent(): void
    {
        $theme = Theme::getInstance();

        echo '<meta name="theme-name" content="' . esc_attr(Theme::NAME) . '">';
        echo '<meta name="theme-version" content="' . esc_attr($theme->getVersion()) . '">';
    }
}
