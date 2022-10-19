<?php

namespace Silmaril\Core\Security;

use Silmaril\Core\Template;

class Destroyer
{
	/**
	 * Muerte del sistema
	 *
	 * @return void
	 */
	public static function messageDie(): void
	{
		(new Template())
			->setFileType('html')
			->setSrc('Core/templates')
			->render('message-die');

		die();
	}

	/**
	 * Eliminar tema
	 *
	 * @return string
	 */
	public function deleteTheme(): string
	{
		return 'Eliminando tema';
	}
}