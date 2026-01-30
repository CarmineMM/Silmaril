<?php

use Silmaril\Core\Foundation\RoadTracer;

if (!function_exists('theme')) {
    /**
     * Theme options
     * 
     * @return Silmaril\Core\Foundation\Theme
     */
    function theme()
    {
        return \Silmaril\Core\Foundation\Theme::getInstance();
    }
}

if (!function_exists('comments_disabled')) {
    /**
     * Verificar si los comentarios están deshabilitados.
     * Aplicables para el front
     */
    function comments_disabled(): bool
    {
        return theme()->anyConfigIsTrue([
            'theme.features.comments.disable_globally',
            'theme.features.comments.disable_front_theme',
        ]);
    }
}

if (!function_exists('categories_disabled')) {
    /**
     * Verificar si las categorías están deshabilitadas
     */
    function categories_disabled(): bool
    {
        return false;
    }
}

if (!function_exists('tags_disabled')) {
    /**
     * Verificar si las etiquetas están deshabilitadas
     */
    function tags_disabled(): bool
    {
        return false;
    }
}


if (!function_exists('roadTracer')) {
    /**
     * Road tracer
     * 
     * @return RoadTracer
     */
    function roadTracer(): RoadTracer
    {
        return RoadTracer::getInstance();
    }
}
