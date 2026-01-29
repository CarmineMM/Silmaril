<?php

/**
 * Silmaril functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Silmaril
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */

require_once dirname(__FILE__) . '/vendor/autoload.php';

defined('ABSPATH') || exit;

\Silmaril\Core\Foundation\Bootstrap::run();

\Silmaril\Core\Foundation\Bootstrap::handleErrors();
