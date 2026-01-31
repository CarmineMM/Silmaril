<?php

namespace Silmaril\Core\Contracts;

interface ServiceProviderInterface
{
    /**
     * Registrar servicios en el contenedor
     */
    public function register(): void;

    /**
     * Bootear servicios después del registro, y despues del inicio de los Servicio y Providers
     */
    public function boot(): void;
}
