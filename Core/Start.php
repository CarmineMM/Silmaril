<?php

namespace Silmaril\Core;

use Silmaril\Core\Support\Configs;

/**
 * Inicializador del tema.
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 2.0.0
 */
final class Start
{
	/**
	 * Foundation Theme
	 *
	 * @var Foundation
	 */
	private Foundation $foundation;

	/**
	 * Construct
	 */
	public function __construct()
	{
		$this->foundation = new Foundation();

		if ( !defined('THEME_VERSION') ) {
			define('THEME_VERSION', wp_get_theme()->get('Version'));
		}
	}

	/**
	 * Ejecutar Core, Toda la ejecución debe estar aquí.
	 *
	 * @return Start
	 */
	public function run(): Start
	{
		// Cargar configuraciones
		$this->foundation->loadConfigs();

		if ( HAS_THEME_CACHE ) {
			return $this;
		}

		$class = class_exists('\Silmaril\Theme\Providers\ThemeProvider')
			? \Silmaril\Theme\Providers\ThemeProvider::class
			: \Silmaril\Core\Providers\Theme::class;

		$class = new $class();

		// Menu principal automático
		if ( $class->getHasPrimaryMenu() ) {
			Configs::add('actions', [
				[
					'action'   => 'init',
					'call'     => [PrimaryMenu::class, 'register'],
					'priority' => 10,
					'args'     => 0,
				],
			]);

			PrimaryMenu::registerFilters();
		}

		return $this;
	}

	/**
	 * Cargar acciones de Wordpress
	 *
	 * @return Start
	 */
	public function wordpressActions(): Start
	{
		if ( !HAS_THEME_CACHE ) {
			$this->foundation->loadActions();
		}
		return $this;
	}

	/**
	 * Carga los widgets dentro de wordpress
	 *
	 * @return $this
	 */
	public function wordpressWidgets(): Start
	{
		if ( !HAS_THEME_CACHE ) {
			$this->foundation->loadWidgets();
		}
		return $this;
	}

	/**
	 * Carga los filtros en wordpress
	 *
	 * @return Start
	 */
	public function wordpressFilters(): Start
	{
		if ( !HAS_THEME_CACHE ) {
			$this->foundation->loadFilters();
		}
		return $this;
	}

	/**
	 * Soporte para el tema
	 *
	 * @return void
	 */
	public static function themeSupport(): void
	{
		foreach (Configs::get('support') as $support => $opt)
		{
			if ( $opt === false ) {
				continue;
			}

			if ( is_bool($opt) ) {
				add_theme_support($support);
				continue;
			}

			if ( is_array($opt) ) {
				add_theme_support($support, $opt);
			}
		}
	}
}