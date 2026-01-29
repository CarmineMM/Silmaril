<?php

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
