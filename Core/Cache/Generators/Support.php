<?php

namespace Silmaril\Core\Cache\Generators;

trait Support
{

    /**
     * Crea el contenido para el archivo de soportes del tema
     *
     * @param array $config
     * @return string
     */
    public function createSupportContent(array $config): string
    {
        $content = '';

        foreach ( $config as $support => $opt ) {
            if ( $opt === false ) {
                continue;
            }

            if ( is_bool($opt) ) {
                $content .= "\nadd_theme_support('{$support}');";
                continue;
            }

            if ( is_array($opt) ) {
                $array = '[';
                foreach ($opt as $i) {
                    $array .= "'{$i}',";
                }
                $array .= ']';
                $content .= "\nadd_theme_support('{$support}', {$array});";
            }
        }

        return $content;
    }
}