<?php

use Silmaril\Core\Support\Str;

/**
 * Debug en la consola
 *
 * @param string|array $msg
 * @package Silmaril Theme
 * @return void
 */
function console_log(...$msg): void
{
	foreach ($msg as $m) {
		\Silmaril\Core\Debug::addMessage($m);
	}
}

/**
 * Debug en la consola, info
 *
 * @param string|array $msg
 * @package Silmaril Theme
 * @return void
 */
function console_info(...$msg): void
{
	foreach ($msg as $m) {
		\Silmaril\Core\Debug::addMessage($m, 'info');
	}
}

/**
 * Debug en la consola, info
 *
 * @param string|array $msg
 * @package Silmaril Theme
 * @return void
 */
function console_error(...$msg): void
{
	foreach ($msg as $m) {
		\Silmaril\Core\Debug::addMessage($m, 'error');
	}
}

/**
 * Debug en la consola, info
 *
 * @param string|array $msg
 * @package Silmaril Theme
 * @return void
 */
function console_warning(...$msg): void
{
	foreach ($msg as $m) {
		\Silmaril\Core\Debug::addMessage($m, 'warning');
	}
}

/**
 * Debug una variable u objeto
 *
 * @param mixed $debug
 * @package Silmaril Theme
 * @return void
 */
function debug(...$debug): void
{
	\Silmaril\Core\Debug::show(...$debug);
}

/**
 * Convertir a collection
 *
 * @param mixed $i
 * @param bool $convertAttributes
 * @package Silmaril Theme
 * @return \Silmaril\Core\Support\Collection
 */
function collect(mixed $i, bool $convertAttributes = true): \Silmaril\Core\Support\Collection
{
	return new \Silmaril\Core\Support\Collection($i, $convertAttributes);
}

/**
 * Obtiene un template part
 *
 * @param string $file Usando Dot notation
 * @param array $args
 * @package Silmaril Theme
 * @return string
 */
function template_part(string $file, $args = []): string
{
	$file = str_replace('.', '/', $file);
	get_template_part("template-parts/{$file}", '', $args);
	return '';
}


if ( !function_exists('view') )
{
	/**
	 * Renderizar una vista, parte de 'template-parts'
	 *
	 * @param string $view
	 * @param array $data
	 * @package Silmaril Theme
	 * @return string
	 */
	function view(string $view, array $data = []): string
	{
		return (new \Silmaril\Core\Template())->render($view, $data);
	}
}

function getUriTheme(string $url): string
{
	return (new \Silmaril\Core\Enqueue())->getUrlFile($url);
}

if ( !function_exists('str') )
{
	/**
	 * Helper de instancia para los Strings
	 *
	 * @param string $string
	 * @package Silmaril Theme
	 * @return \Silmaril\Core\Support\Str
	 */
	function str(string $string): \Silmaril\Core\Support\Str
	{
		return new Str($string);
	}
}


if ( !function_exists('getImageFromArray') )
{
	/**
	 * Obtiene la URL de un archivo a partir de la desestructuración
	 * Del objeto guardado en 'meta_value' ('meta_key' = '_wp_attachment_metadata') de la tabla wp_postmeta
	 *
	 * @param array $attachment
	 * @param string $size
	 * @return string
	 * @package Silmaril Theme
	 */
	function getAttachmentUrl(array $attachment, string $size = 'thumbnail'): string
	{
		$uploads = wp_get_upload_dir();

		if ( !$uploads || $uploads['error'] ) {
			return __('No se pudo obtener la imagen');
		}

		// Tamaños aceptados por wordpress
		$sizes = ['thumbnail', 'medium'];

		$relativePath = _wp_get_attachment_relative_path($attachment['file']) . '/';
		$path = $uploads['baseurl'] . '/' . $relativePath;
		$image = ''; // Imagen seleccionada

		if ( in_array($size, $sizes) && count($attachment['sizes']) > 1 ) {
			$image = isset($attachment['sizes'][$size])
				? $attachment['sizes'][$size]['file']
				: '';
		}

		// Imagen por defecto
		if ( $image === '' ) {
			$image = str_replace($relativePath, '', $attachment['file']);
		}

		return $path.$image;
	}
}


/**
 * Muestra el Post type formateado y con icono
 *
 * @param string $post_type
 * @return string
 */
function showPostTypeWithIcon(string $post_type): string
{
	return match ($post_type) {
		'peliculas' => '<i class="bi bi-camera-video"></i> Película',
		'series'    => '<i class="bi bi-camera-reels"></i> Serie',
		'animes'    => '<i class="bi bi-emoji-kiss"></i> Anime',
		'software'  => '<i class="bi bi-windows"></i> Software',
		'cursos'    => '<i class="bi bi-calendar2-check"></i> Curso',
		'libros'    => '<i class="bi bi-journal"></i> Libro',
		'juegos'    => '<i class="bi bi-joystick"></i> Juego',
		'musicas'   => '<i class="bi bi-music-note-beamed"></i> Musica',
		'videos'    => '<i class="bi bi-fast-forward"></i> Video',
		default => $post_type,
	};
}