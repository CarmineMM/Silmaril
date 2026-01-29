<?php

namespace Silmaril\Core\Foundation;

use Silmaril\Core\Helpers\Filesystem;
use CarmineMM\UnitsConversion\Conversion\TimeConversion;

class Bootstrap
{
    /**
     * Bootstrap the application
     */
    public static function run(): void
    {
        // Definir tiempo de inicio para medir la duración de la ejecución
        if (!\defined('TIMELINE_START')) {
            \define('TIMELINE_START', \microtime(true));
        }

        // Inicializar tema después de que WordPress esté listo
        \add_action('after_setup_theme', [self::class, 'init'], 1);

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Bootstrap'),
            'line' => 10,
            'total_time' => TimeConversion::fromMilliseconds(\microtime(true) - TIMELINE_START),
            'function' => 'run',
            'class' => Bootstrap::class,
            'method' => Bootstrap::class . '->run()',
            'object' => Bootstrap::class,
            'args' => [],
        ]);
    }

    /**
     * Inicialización principal
     */
    public static function init(): void
    {
        $theme = Theme::getInstance();

        $theme->bootstrap();

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Bootstrap'),
            'line' => 38,
            'function' => 'init',
            'class' => Bootstrap::class,
            'method' => Bootstrap::class . '->init()',
            'object' => Bootstrap::class,
            'args' => [],
        ]);
    }

    /**
     * Manejar excepciones en desarrollo
     */
    public static function handleErrors(): void
    {
        if (Theme::getInstance()->config('theme.debug', default: false)) {
            \error_reporting(E_ALL);
            \ini_set('display_errors', 1);
        }

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Core/Foundation/Bootstrap'),
            'line' => 57,
            'function' => 'handleErrors',
            'class' => Bootstrap::class,
            'method' => Bootstrap::class . '->handleErrors()',
            'object' => Bootstrap::class,
            'args' => [],
        ]);
    }
}
