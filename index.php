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
use Silmaril\Core\Foundation\Theme;

get_header();

?>

<pre>
    <?php var_dump(theme()->config()); ?>
</pre>

<?php
get_footer();
