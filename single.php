<?php
/**
 * The template for displaying single post
 *
 * Archivo para los post types individuales
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package crm
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */


get_header();

the_post();
?>

<header class="min-h-400 max-h-400 overflow-hidden bg-img-cover mb-4 header-blog" style="background-image: url(<?= get_the_post_thumbnail_url() ?>)">
    <div class="text-white container text-center">
        <?php the_title('<h1 class="fw-bold display-3">', '</h1>'); ?>
    </div>
</header>

<div class="container">
    <div class="row">
        <main class="col-12 col-lg-8 pe-lg-4 pe-xl-5 mb-5">
            <section>
                <!-- TODO: Trabajar autores -->
            </section>
            <?php the_content(); ?>
        </main>
        <aside class="col-12 col-lg-4">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</div>

<?php

get_footer();