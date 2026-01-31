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
        //     'type' => 'App',
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

    /**
     * Resumen
     * 
     * @param string|null $type
     * @return array
     */
    public static function resumen(?string $type = null): array
    {
        if (!WP_DEBUG || (Theme::hasInizialice() && !Theme::getInstance()->config('theme.road_tracer', default: true))) {
            return [];
        }

        $tracer = self::getInstance()->tracer;

        if ($type !== null) {
            $tracer = Arr::where(
                $tracer,
                fn($tracer) => $tracer['type'] === $type
            );
        }

        return Arr::map(
            $tracer,
            fn($tracer) =>
            [
                'method' => $tracer['method'],
                'total_time' => $tracer['total_time']->display('ms', 4),
                'type' => $tracer['type'],
            ]
        );
    }

    /**
     * Add tracer
     * 
     * @param array $tracer
     * @return void
     */
    public static function stroke(array $tracer): void
    {
        if (!WP_DEBUG || (Theme::hasInizialice() && !Theme::getInstance()->config('theme.road_tracer', default: true))) {
            return;
        }

        if ($countTotal = \count(self::getInstance()->tracer)) {
            $previousTracer = self::getInstance()->tracer[$countTotal - 1];

            if (!isset($previousTracer['microtime'])) {
                $previousTracer['microtime'] = \microtime(true);
            }

            $tracer['total_time'] = TimeConversion::fromMilliseconds(\microtime(true) - $previousTracer['microtime']);
        }

        if (!isset($tracer['type'])) {
            $tracer['type'] = match (true) {
                \str_contains($tracer['class'], 'Provider') => 'ServiceProvider',
                \str_contains($tracer['class'], 'Service') => 'Service',
                \str_contains($tracer['class'], 'Cache') => 'Cache',
                \str_contains($tracer['class'], 'Foundation') => 'Foundation',
                default => 'App',
            };
        }

        self::getInstance()->tracer[] = $tracer;
    }
}
