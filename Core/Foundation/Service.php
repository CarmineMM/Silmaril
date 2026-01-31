<?php

namespace Silmaril\Core\Foundation;

use Silmaril\Core\Contracts\ServiceInterface;

abstract class Service implements ServiceInterface
{
    public function __construct(
        public Theme $theme
    ) {}

    abstract public function init(): void;
}
