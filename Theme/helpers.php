<?php

/**
 * Alterna el Content, devuelto por Wordpress.
 *
 * @param $post_id
 * @param $more_link_text
 * @param bool $strip_teaser
 *
 * @return void
 */
function silmaril_the_content($post_id = null, $more_link_text = null, bool $strip_teaser = false): void
{
	if (is_bool($post_id)) {
		global $wp_query;
		$post_id = $wp_query->queried_object_id;
	}

	$content = get_the_content($more_link_text, $strip_teaser, $post_id);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}
