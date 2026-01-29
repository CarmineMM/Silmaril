<?php

namespace Silmaril\Core\Contracts;

use Silmaril\Core\Foundation\Theme;

interface ContentInterface
{
    public function __construct(Theme $theme);

    /**
     * Registrar el contenido
     */
    public function register(array $content = []): void;
}
