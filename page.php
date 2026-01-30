<?php

/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Silmaril
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */

use Silmaril\Core\Foundation\RoadTracer;

get_header();

the_content();
// dump(RoadTracer::resumen());
get_footer();
