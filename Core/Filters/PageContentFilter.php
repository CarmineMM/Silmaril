<?php

namespace Silmaril\Core\Filters;

use Silmaril\Core\Foundation\Theme;

class PageContentFilter
{
    /**
     * Hook para el wp_head
     * 
     * @return void
     */
    public static function addBodyClasses(array $classes): array
    {
        $classes[] = 'theme-version-' . str_replace('.', '-', Theme::getInstance()->getVersion());

        return $classes;
    }
}
