<?php

namespace Silmaril\Core\Foundation;

use CarmineMM\UnitsConversion\Conversion\TimeConversion;
use Silmaril\Core\Contracts\ContentInterface;
use Silmaril\Core\Helpers\Filesystem;

class ContentBootstrap implements ContentInterface
{
    public function __construct(
        public Theme $theme
    ) {}

    public function register(array $content = []): void
    {
        //...
    }

    /**
     * Call Content Register method
     * 
     * @param Theme $theme
     * @param string $content
     * @param array $args
     * @throws \Exception
     * @return void
     */
    public static function call(Theme $theme, string $content, array $args = []): void
    {
        $contentInstance = new $content($theme);

        if (!method_exists($contentInstance, 'register')) {
            throw new \Exception("El contenido '{$content}' no tiene el método 'register'");
        }

        $contentInstance->register($args);

        RoadTracer::stroke([
            'file' => Filesystem::phpFile('Code/Foundation/ContentBootstrap'),
            'line' => 10,
            'total_time' => TimeConversion::fromMilliseconds(\microtime(true) - TIMELINE_START),
            'function' => 'call',
            'class' => $content::class,
            'method' => $content::class . '->register(array $content)',
            'object' => $content::class,
            'args' => [
                'theme' => $theme,
                'content' => $content,
                'args' => $args,
            ],
        ]);
    }
}
