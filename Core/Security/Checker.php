<?php

namespace Silmaril\Core\Security;

/**
 * Comprueba ciertas cosas en el tema
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
class Checker
{
	/**
	 * Token de seguridad para la API
	 *
	 * @var string
	 */
	private string $securityToken = '24367392';

	/**
	 * Verificar autor del tema
	 *
	 * @return bool
	 */
	public static function checkAuthor(): bool
	{
		if ( wp_get_theme()->get('Author') !== 'Carmine Maggio' ) {
			Destroyer::messageDie();
		}

		return true;
	}

}