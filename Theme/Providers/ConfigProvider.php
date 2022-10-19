<?php

namespace Silmaril\Theme\Providers;

use Silmaril\Core\Config;

class ConfigProvider extends Config
{
	/**
	 * Path hacia los archivos de configuración
	 *
	 * @var string
	 */
	protected string $configPath = 'Theme/config';

	/**
	 * Registrar archivos de configuración
	 *
	 * @return array
	 */
	public function loadConfigFiles(): array
	{
		return [
			'actions',
			'enqueue',
			'support',
			'sidebars',
		];
	}
}