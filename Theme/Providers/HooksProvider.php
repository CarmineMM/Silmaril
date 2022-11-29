<?php

namespace Silmaril\Theme\Providers;

use Silmaril\Theme\Menus\Socials;

/**
 * Hooks de Wordpress llamados mas fácilmente
 */
class HooksProvider
{
	/**
	 * Al inicio de la ejecución de wordpress
	 *
	 * @see https://developer.wordpress.org/reference/hooks/init/
	 * @return void
	 */
	public static function init(): void
	{
        //
	}

	/**
	 * Ejecución durante la carga.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/after_setup_theme/
	 * @return void
	 */
	public static function run(): void
	{
		//
	}

	/**
	 * Se ejecuta cuando el tema es activado por primera vez.
	 * Perfecto para crear bases de datos, entre otras cosas,
	 * de una única ejecución.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/after_switch_theme/
	 * @return void
	 */
	public static function activateTheme(): void
	{
		//
	}
}