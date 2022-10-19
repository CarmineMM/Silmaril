<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #app div and all content after. <div id="app">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Silmaril
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
?>

    <footer>
        <div class="text-center">
            Copyright &copy; <span $-text="(new Date()).getFullYear()"></span> <?php bloginfo('site-title') ?></a>
        </div>
    </footer>

</div><!-- #app -->

<?php wp_footer(); ?>

</body>
</html>