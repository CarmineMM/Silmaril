<?php

namespace Silmaril\Core;

use Silmaril\Core\Support\Configs;
use DateTime;

/**
 * Carga de los scripts y estilos en los queues
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
class Enqueue
{
	/**
	 * Cargar los estilos y scripts
	 *
	 * @return void
	 */
	public static function load(): void
	{
		$set = static::sets();

		(new self)
			->loadStyles( $set['css'] ?? [] )
			->loadScripts( $set['js'] ?? [] );
	}

	/**
	 * Cargar archivos en el admin
	 *
	 * @return void
	 */
	public static function loadInAdmin(): void
	{
		$set = static::sets('admin-');

		(new self)
			->loadStyles( $set['css'] ?? [] )
			->loadScripts( $set['js'] ?? [] );
	}

	/**
	 * Carga estilos
	 *
	 * @param array $styles
	 * @return Enqueue
	 */
	public function loadStyles(array $styles): static
	{
		foreach ($styles as $style) {
			if ( empty($style['url']) ) {
				continue;
			}

			wp_enqueue_style(
				$style['key'],
				$style['url'],
				$style['deps'],
				$style['ver'],
				$style['media']
			);
		}

		return $this;
	}

	/**
	 * Carga scripts
	 *
	 * @param array $scripts
	 * @return $this
	 */
	public function loadScripts(array $scripts): static
	{
		foreach ($scripts as $key => $script) {
			if ( empty($script['url']) ) {
				continue;
			}

			wp_enqueue_script(
				$script['key'],
				$script['url'],
				$script['deps'],
				$script['ver'],
				$script['footer'],
			);
		}

		return $this;
	}

	/**
	 * Obtiene el url del archivo
	 *
	 * @param string|array $url
	 * @return string
	 */
	public function getUrlFile(string|array $url): string
	{
		if ( is_array($url) ) {
			// Primera para desarrollo, segunda para producción
			$url = WP_DEBUG ? $url[0] : $url[1];
		}

		if ( empty($url) ) {
			return '';
		}

		return str_contains($url, 'http') ? $url : get_theme_file_uri($url);
	}

	/**
	 * Version del script, de forma dinámica
	 *
	 * @param string $ver
	 * @param string $url
	 * @return string
	 */
	public function version(string $ver = '', string $url = ''): string
	{
		// Version
		if ( str_contains($url, 'https') ) {
			return $ver;
		}

		if ( WP_DEBUG ) {
			return (new DateTime())->getTimestamp();
		}

		return $ver === '' ? THEME_VERSION : $ver;
	}

	/**
	 * Devolver hojas de estilos, solo para desarrollo
	 * y para usuarios autenticados.
	 *
	 * @param string $prefix
	 * @return array
	 */
	public static function sets(string $prefix = ''): array
	{
		$config = Configs::get('enqueue');
		$return = [];
		$allow = ["{$prefix}css", 'all-css', "{$prefix}js", 'all-js'];

		if ( is_user_logged_in() && WP_DEBUG ) {
			array_push($allow, 'debug-css', 'debug-js');
		}

		foreach ($allow as $a) {
			if ( !array_key_exists($a, $config) ) {
				continue;
			}

			if ( str_contains($a, 'css') ) {
				foreach ($config[$a] as $css) {
					$return['css'][] = $css;
				}
			}
			else if ( str_contains($a, 'js') ) {
				foreach ($config[$a] as $js) {
					$return['js'][] = $js;
				}
			}
		}

		return $return;
	}
}