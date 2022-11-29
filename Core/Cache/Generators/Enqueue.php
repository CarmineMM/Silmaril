<?php

namespace Silmaril\Core\Cache\Generators;

use Silmaril\Core\Support\Str;

trait Enqueue
{
    /**
     * Nombre de las funciones para los scripts y estilos,
     * en el panel y cargadas globalmente.
     *
     * @var string
     */
    private string $funcStylesScripts = 'enqueue_styles_scripts';
    private string $funcAdminStylesScripts = 'enqueue_styles_scripts_admin';

    /**
     * Nombre de la función en para la carga de scripts y estilos
     *
     * @return string
     */
    public function getNameFuncScripts(): string
    {
        return TEXT_DOMAIN .'_'. $this->funcStylesScripts;
    }

    /**
     * Nombre de la función en para la carga de scripts y estilos
     *
     * @return string
     */
    public function getNameFuncScriptsAdmin(): string
    {
        return TEXT_DOMAIN .'_'. $this->funcAdminStylesScripts;
    }

    /**
     * Crea el contenido para las queues
     *
     * @param array $config
     * @return string
     */
    public function createEnqueueContent(array $config): string
    {
        $funcScripts = "\nfunction {$this->getNameFuncScripts()}(): void {";
        $funcScriptsAdmin = "\nfunction {$this->getNameFuncScriptsAdmin()}(): void {";

        foreach ( $config as $type => $queues )
        {
            $type = Str::of($type);

            // No guardar las de debug
            if ( $type->contains('debug') ){
                continue;
            }

            foreach ($queues as $key => $queue) {
                // Los cargados en el admin y en all
                if ( $type->contains(['all', 'admin']) ) {
                    $funcScriptsAdmin .= $this->extractQueue($queue, $type->replace(['admin-', 'all-'], '')->toString());
                }

                // Cargados en el frontend y en all, menos en el admin
                if ( !$type->contains('admin') ) {
                    $funcScripts .= $this->extractQueue($queue, $type->replace(['all-'], '')->toString());
                }
            }
        }

        $funcScripts .= "\n}";
        $funcScriptsAdmin .= "\n}";

        return $funcScriptsAdmin.$funcScripts;
    }

    /**
     * Extrae el queue
     *
     * @param $queue
     * @param string $file
     * @return string
     */
    private function extractQueue($queue, string $file): string
    {
        $deps = $this->generateDeps($queue['deps']);

        // Footer
        $footer = 'false';
        if ( isset($q['footer']) ){
            $footer = $q['footer'] ? 'true' : 'false';
        }

        return match ($file) {
            'js'    => "\n\twp_enqueue_script('{$queue['key']}', '{$queue['url']}', {$deps}, '{$queue['ver']}', {$footer});",
            'css'   => "\n\twp_enqueue_style('{$queue['key']}', '{$queue['url']}', {$deps}, '{$queue['ver']}', '{$queue['media']}');",
            default => '',
        };
    }
}