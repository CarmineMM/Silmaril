<?php

/**
 * Sidebar, o widgets en el sistema
 *
 * @see https://developer.wordpress.org/reference/functions/register_sidebar/
 */
return [
	[
		'name'         => 'Sidebar del Blog',
		'id'           => 'sidebar-blog',
		'description'  => 'Sidebar ubicado en las entradas del Blog',
		'before_title' => '<h4 class="mb-3">',
		'after_title'  => '</h4>',
		'before_widget' => '<div>',
		'after_widget' => '</div>',
	]
];