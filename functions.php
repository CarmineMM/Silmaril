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

defined( 'ABSPATH' ) || exit;

// Inicia el tema
$startTheme = new \Silmaril\Core\Start();

// Ejecutar, según línea de Tiempo
// A pesar de la ejecución de Wordpress
// Se plasma una ejecución secuencial del tema.
$startTheme->run()

    // Widgets o sidebar del sistema
    ->wordpressWidgets()

    // Acciones de Wordpress registradas
    ->wordpressActions()

	// Filtros de wordpress
	->wordpressFilters();