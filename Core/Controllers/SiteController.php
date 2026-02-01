<?php

namespace Silmaril\Core\Controllers;

use Illuminate\Support\Arr;
use Silmaril\Core\Foundation\BaseController;
use WP_REST_Request;
use WP_REST_Response;

class SiteController extends BaseController
{
    /**
     * Keys to get in options site
     */
    public array $keysToGet = [];

    /**
     * Init controller 
     * 
     * @return void
     */
    public function init(): void
    {
        foreach ($this->theme->config('api.site_config') as $key => $value) {
            if (\is_array($value) && ($value['enabled'] ?? false)) {
                $this->keysToGet[$key] = \array_filter($value);
            }
        }
    }

    /**
     * Get all site config
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getSiteConfig(WP_REST_Request $request): WP_REST_Response
    {
        $config = $this->theme->callServiceMethod('site', 'getSiteConfig', [
            'ttl' => 1800,
            ...array_map(fn($item) => true, $this->keysToGet)
        ]);

        // Filtrar a solo el valor seleccionado
        $filter = Arr::map(
            array: array_filter($config, \is_array(...)),
            callback: fn($item, $key) => $key === 'theme_mods' && isset($this->keysToGet[$key])
                ? $item
                : Arr::only($item, \array_keys($this->keysToGet[$key]))
        );

        return new WP_REST_Response(
            data: [
                'success' => true,
                'data' => $filter,
                'meta' => [
                    'cache' => $config['cache'] ?? false,
                    'timestamp' => $config['timestamp'] ?? time(),
                    'route' => $request->get_route(),
                ],
            ],
            status: 200
        );
    }

    /**
     * Get site config by type 
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getSiteConfigByType(WP_REST_Request $request): WP_REST_Response
    {
        $type = $request->get_param('type');
        $keys = \array_keys($this->keysToGet);

        if (!$type || !\in_array($type, $keys)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Type parameter is required',
            ], 400);
        }

        $config = $this->theme->callServiceMethod('site', 'getSiteConfig', [
            'ttl' => 1800,
            $type => true,
        ]);

        return new WP_REST_Response([
            'success' => true,
            'data' => Arr::only($config[$type], \array_keys($this->keysToGet[$type])),
            'meta' => [
                'cache' => $config['cache'] ?? false,
                'timestamp' => $config['timestamp'] ?? time(),
                'route' => $request->get_route(),
            ],
        ]);
    }
}
