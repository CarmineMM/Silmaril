<?php

namespace Silmaril\Core\Controllers;

use Silmaril\Core\Foundation\BaseController;
use WP_REST_Request;
use WP_REST_Response;

class MenuController extends BaseController
{
    /**
     * Get menu
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getMenus(WP_REST_Request $request): WP_REST_Response
    {
        return new WP_REST_Response([
            'not' => [],
        ]);
    }
}
