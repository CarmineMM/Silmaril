<?php
/**
 * The template for displaying all pages
 *
 * Incluso la front-page estará en este archivo
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Silmaril
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */


get_header();

echo '<main class="container">';

the_content();

echo '</main>';

get_footer();
