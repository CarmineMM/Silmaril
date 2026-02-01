<?php

namespace Silmaril\Core\Foundation;

use Silmaril\Core\Contracts\ControllerInterface;

class BaseController implements ControllerInterface
{
    public function __construct(public Theme $theme) {}

    public function init(): void {}
}
