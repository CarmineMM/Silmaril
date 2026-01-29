<?php

namespace Silmaril\Core\Services;

use Silmaril\Core\Contents\AssetsContent;
use Silmaril\Core\Foundation\Service;

class AssetService extends Service
{
    public function init(): void
    {
        $this->frontendStyles();
    }

    private function frontendStyles(): void
    {
        foreach ($this->theme->config('assets.frontend.styles') as $key => $value) {
            // TODO: Seguir aqui
            // die(var_dump($value));
        }
    }
}
