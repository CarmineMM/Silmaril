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
     * Habilitar el uso de post types
     *
     * @var bool
     */
    protected bool $usePostTypes = true;

    /**
     * Habilitar el uso de taxonomies
     *
     * @var bool
     */
    protected bool $useTaxonomies = true;

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
            'taxonomies',
            'post_types',
		];
	}
}