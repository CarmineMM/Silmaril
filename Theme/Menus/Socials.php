<?php

namespace Silmaril\Theme\Menus;

class Socials
{
	/**
	 * Nombre del menu
	 */
	const name = 'socials';

	/**
	 * Menu para las redes sociales
	 *
	 * @return void
	 */
	public static function register(): void
	{
		register_nav_menu(self::name, __('Redes sociales en el footer', TEXT_DOMAIN));
	}
}