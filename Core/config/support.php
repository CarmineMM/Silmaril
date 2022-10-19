<?php

/**
 * Soporte permitido en el tema
 *
 * @see https://developer.wordpress.org/reference/functions/add_theme_support/
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
return [
	/**
	 * Título dinámico del sitio.
	 */
	'title-tag' => true,

	/**
	 * Logo personalizado
	 */
	'custom-logo' => true,

	/**
	 * Imágenes destacadas en los post types.
	 */
	'post-thumbnails' => true,

	/**
	 * Contenido de los bloques de Gutenberg anchos.
	 */
	'align-wide' => true,

	/**
	 * Contenido incrustado receptivo.
	 * Como youtube, vimeo, etc..
	 */
	'responsive-embeds' => true,

	/**
	 * Control de colores en enlaces (legacy).
	 * Versión minima de WordPress: 5.9
	 */
	'experimental-link-color' => true,

	/**
	 * Estilos de Gutenberg.
	 */
	'wp-block-styles' => true,

	/**
	 * Agrega soporte para formatos de posts.
	 *
	 * @see https://wordpress.org/support/article/post-formats/#:~:text=Post%20Formats%20is%20a%20theme,themes%20that%20support%20the%20feature.
	 */
	'post-formats' => ['aside', 'image'],

	/**
	 * Agrega enlaces predeterminados de fuentes RSS
	 * de publicaciones y comentarios a la cabecera.
	 */
	'automatic-feed-links' => true,

	/**
	 * Agrega compatibilidad con temas para la actualización selectiva de widgets.
	 *
	 * @see https://make.wordpress.org/core/2016/03/22/implementing-selective-refresh-support-for-widgets/
	 */
	'customize-selective-refresh-widgets' => true,
];