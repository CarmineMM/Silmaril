<?php

namespace Silmaril\Core\Contents;

use Silmaril\Core\Support\Collection;
use Silmaril\Core\Support\Str;

class Contents
{
    /**
     * Nombre
     *
     * @param array $names
     * @return Collection
     * @throws \Exception
     */
    public function expectedNames(array $names): Collection
    {
        // Nombres pasados en Singular y (opcional) plural
        $names = new Collection($names);

        if ( !$names->has('singular') ) {
            throw new \Exception('Es necesario especificar un nombre en singular al menos');
        }

        // Plural automático
        if ( !$names->has('plural') ) {
            $names->add(
                'plural',
                (new Str($names->get('singular')))->plural()->toString()
            );
        }

        return $names;
    }
}