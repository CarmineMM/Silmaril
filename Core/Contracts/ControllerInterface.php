<?php

namespace Silmaril\Core\Contracts;

use Silmaril\Core\Foundation\Theme;

interface ControllerInterface
{
    public function __construct(Theme $theme);

    public function init(): void;
}
