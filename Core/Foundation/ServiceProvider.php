<?php

namespace Silmaril\Core\Foundation;

use Silmaril\Core\Contracts\ServiceProviderInterface;

abstract class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Instancia del tema
     */
    protected Theme $theme;

    /**
     * Constructor
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Registrar servicios
     */
    abstract public function register(): void;

    /**
     * Bootear servicios
     */
    abstract public function boot(): void;
}
