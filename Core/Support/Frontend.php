<?php

namespace Silmaril\Core\Support;

class Frontend
{
	/**
	 * Logo personalizado del sitio
	 *
	 * @param array $params
	 * @return string
	 */
	public static function logo(array $params = []): string
	{
		$options = new Collection([
			'height' => '4rem',
			'class'  => 'navbar-brand'
		]);

		$options->combine($params);

		// Logo personalizado
		if( has_custom_logo() ) {
			$logo = str_replace('custom-logo-link', 'custom-logo-link '.$options->get('class'), get_custom_logo());
			return preg_replace(
				'/(width|height)=\"\d*\"\s/',
				"style='max-height: {$options->get('height')}; height: {$options->get('height')}'",
				$logo
			);
		}

		// Si no hay log, usar el nombre de la pagina
		return sprintf(
			'<a class="%s" href="%s"><h2>%s</h2></a>',
			$options->get('class'),
			home_url('/'),
			esc_html(get_bloginfo('name')),
		);
	}

	/**
	 * Limpiar el Height y Width de las imágenes
	 * renderizadas por wordpress
	 *
	 * @param string $html
	 * @return string
	 */
	public static function clearWidthHeightWordpress(string $html): string
	{
		return preg_replace('/(width|height)="\d*"\s/', "", $html);
	}

	/**
	 * Usar Vite
	 *
	 * @return void
	 */
	public static function useVite(): void
	{
		$provider = class_exists('\Silmaril\Theme\Providers\ThemeProvider')
			? new \Silmaril\Theme\Providers\ThemeProvider()
			: new \Silmaril\Core\Providers\Theme();

		$vite = $provider->getViteClient();
		$main = $provider->getViteScript();

		echo "<script type='module' crossorigin src='{$vite}'></script>";
		echo "<script type='module' crossorigin src='{$main}'></script>";
	}
}