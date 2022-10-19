<?php

global $wp_query;

echo '<header>';
silmaril_the_content(true);
echo '</header>';

$first = 1;

echo '<main class="container py-5 blog-site">';
while (have_posts()):
	the_post();
	if ( $first === 1 ): // Primera publicación ?>

	<article class="card shadow mb-5">
		<?php the_post_thumbnail('large', ['class' => 'card-img-top first-img']); ?>
		<div class="card-body">
			<?php
				the_title('<h2 class="card-title mb-0">', '</h2>');
				the_date('', '<time class="mb-3 d-block">', '</time>');
				the_excerpt();
			?>
			<a href="<?= esc_url(get_the_permalink()) ?>" class="btn btn-primary px-4">Leer Más</a>
		</div>
	</article>

	<section class="row row-cols-1 row-cols-md-3">

	<?php else: // Segunda publicación, tercera, cuarta... ?>

    <div class="p-3">
        <article class="card shadow">
            <?php the_post_thumbnail('medium', ['class' => 'card-img-top']); ?>
            <div class="card-body">
                <?php
                    the_title('<h2 class="card-title mb-0">', '</h2>');
                    the_date('', '<time class="mb-3 d-block">', '</time>');
                    the_excerpt();
                ?>
                <a href="<?= esc_url(get_the_permalink()) ?>" class="btn btn-primary px-4">Leer Más</a>
            </div>
        </article>
    </div>
<?php
    if ( $first === 1 ) {
	    echo '</section><!-- .row-cols-* -->';
    }
	endif;
	$first++;
endwhile;
echo '</main>';
wp_reset_postdata();
