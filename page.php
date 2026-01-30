<?php

/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Silmaril
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */

get_header();

the_content();
dump(theme()->config());
get_footer();
