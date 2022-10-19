<?php

use Silmaril\Core\Support\Frontend;

/**
 * Filtros en wordpress
 *
 * @see https://developer.wordpress.org/plugins/hooks/filters/
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
return [
	/**
	 * Limpiar los width y height de los thumbnails
	 *
	 * @see https://developer.wordpress.org/reference/hooks/post_thumbnail_html/
	 */
	[
		'filter'   => 'post_thumbnail_html',
		'call'     => [Frontend::class, 'clearWidthHeightWordpress'],
		'priority' => 10,
		'args'     => 1,
	],

	/**
	 * Limpiar los width y height de las imágenes en el editor
	 *
	 * @see https://developer.wordpress.org/reference/hooks/image_send_to_editor/
	 */
	[
		'filter'   => 'image_send_to_editor',
		'call'     => [Frontend::class, 'clearWidthHeightWordpress'],
		'priority' => 10,
		'args'     => 1,
	],

	/**
	 * Limpiar los width y height del contenido de las publicaciones.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/image_send_to_editor/
	 */
	[
		'filter'   => 'the_content',
		'call'     => [Frontend::class, 'clearWidthHeightWordpress'],
		'priority' => 10,
		'args'     => 1,
	],
];