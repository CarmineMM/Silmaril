<?php

namespace Silmaril\Core\Hooks;

use Silmaril\Core\Foundation\Theme;
use WP_Error;

class RestApiHook
{
    /**
     * Disable REST API
     */
    public static function disabledApiRest(): void
    {
        // 1. Bloquear acceso público (solo usuarios logueados)
        \add_filter('rest_authentication_errors', function ($result) {
            if (!empty($result)) {
                return $result;
            }
            if (!is_user_logged_in()) {
                return new WP_Error('rest_private', 'Acceso restringido.', array('status' => 401));
            }
            return $result;
        });

        // 2. Limpiar el HTML (quitar enlaces a la API del <head>)
        \remove_action('wp_head', 'rest_output_link_wp_head', 10);
        \remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
        \remove_action('template_redirect', 'rest_output_link_header', 11);
    }

    /**
     * Configure the cors for the REST API
     * 
     * @return mixed
     */
    public static function configureCors($served, $result, $request, $server): mixed
    {
        $cors = Theme::getInstance()->config('api.cors', []);

        // Allowed origins
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        $allowed_origins = $cors['allowed_origins'] ?? ['*'];

        if (\in_array('*', $allowed_origins) || \in_array($origin, $allowed_origins)) {
            \header("Access-Control-Allow-Origin: {$origin}");
        }

        // Otros headers CORS
        \header('Access-Control-Allow-Methods: ' . implode(', ', $cors['allowed_methods'] ?? []));
        \header('Access-Control-Allow-Headers: ' . implode(', ', $cors['allowed_headers'] ?? []));
        \header('Access-Control-Expose-Headers: ' . implode(', ', $cors['expose_headers'] ?? []));
        \header('Access-Control-Max-Age: ' . ($cors['max_age'] ?? 86400));

        if ($cors['allow_credentials'] ?? true) {
            \header('Access-Control-Allow-Credentials: true');
        }

        // Manejar preflight OPTIONS request
        if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
            \status_header(200);
            exit;
        }

        return $served;
    }

    /**
     * Check rate limit for the REST API
     * 
     * @param mixed $result
     * @param mixed $server
     * @param mixed $request
     * @return mixed
     */
    public static function checkRateLimit($result, $server, $request): mixed
    {
        $config = Theme::getInstance()->config('api.rate_limit');
        $limit = $config['limit'] ?? 100;
        $interval = $config['interval'] ?? 3600;

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cache_key = 'silmaril_api_rate_limit_' . md5($ip);

        $requests = \get_transient($cache_key) ?: 0;

        if ($requests >= $limit) {
            return new WP_Error(
                'rate_limit_exceeded',
                'Demasiadas solicitudes. Intenta nuevamente más tarde.',
                ['status' => 429]
            );
        }

        set_transient($cache_key, $requests + 1, $interval);

        // Añadir headers de rate limiting
        header('X-RateLimit-Limit: ' . $limit);
        header('X-RateLimit-Remaining: ' . ($limit - $requests - 1));
        header('X-RateLimit-Reset: ' . (time() + $interval));

        return $result;
    }
}
