<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Providers\Vite;

class Theme extends Vite
{
	/**
	 * Hacer uso del menu principal,
	 * por defecto
	 *
	 * @var bool
	 */
	protected bool $hasPrimaryMenu = true;

	/**
	 * Obtiene si se desea un primary menu
	 *
	 * @return bool
	 */
	public function getHasPrimaryMenu(): bool
	{
		return $this->hasPrimaryMenu;
	}
}