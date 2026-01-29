<?php

namespace Silmaril\Core\Foundation;

use CarmineMM\UnitsConversion\Conversion\TimeConversion;
use Illuminate\Support\Arr;
use Silmaril\Core\Contracts\RoadTracerInterface;
use Silmaril\Core\Helpers\Filesystem;

class RoadTracer
{
    public static ?RoadTracer $instance = null;

    /**
     * List tracer
     * 
     * @var array
     */
    public array $tracer = [
        // [
        //     'file' => '',
        //     'line' => null,
        //     'function' => null,
        //     'class' => null,
        //     'method' => null,
        //     'object' => null,
        //     'args' => [],
        // ]
    ];

    /**
     * Intance Tracer
     * 
     * @return RoadTracer
     */
    public static function getInstance(): RoadTracer
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get tracer
     * 
     * @return array
     */
    public function getTracer(): array
    {
        return $this->tracer;
    }

    public static function resumen(): array
    {
        return Arr::map(
            self::getInstance()->tracer,
            fn($tracer) =>
            [
                'method' => $tracer['method'],
                'total_time' => $tracer['total_time']->display('ms', 4),
            ]
        );
    }

    /**
     * Add tracer
     * 
     * @param array $tracer
     * @return void
     */
    public static function stroke(array $tracer, $calculateTime = true): void
    {
        if (!WP_DEBUG || (Theme::hasInizialice() && !Theme::getInstance()->config('theme.road_tracer', default: true))) {
            return;
        }

        if ($calculateTime && $countTotal = \count(self::getInstance()->tracer)) {
            $previousTracer = self::getInstance()->tracer[$countTotal - 1];

            if (!isset($previousTracer['microtime'])) {
                $previousTracer['microtime'] = \microtime(true);
            }

            $tracer['total_time'] = TimeConversion::fromMilliseconds(\microtime(true) - $previousTracer['microtime']);
        }

        self::getInstance()->tracer[] = $tracer;
    }

    /**
     * Agrega trazos iniciales del tema que se ejecutaron antes de la instancia del road tracer
     * 
     * @return void
     */
    public function themeStrokes(): void
    {
        // Bootstrap

    }
}
