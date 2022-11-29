<?php

namespace Silmaril\Core;

use Silmaril\Core\Support\Configs;
use Silmaril\Core\Support\Frontend;
use Silmaril\Core\Cache\Generator;

/**
 * Define las configuraciones
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 1.5.0
 */
class Config extends Generator
{
	/**
	 * Carpeta donde están las configuraciones por defecto
	 *
	 * @var string
	 */
	protected string $configPath = 'Core/config';

	/**
	 * Uso de Vite
	 *
	 * @var bool
	 */
	protected bool $vite = true;

	/**
	 * Carga de estilos y scripts por defecto
	 *
	 * @var bool
	 */
	protected bool $defaultEnqueue = true;

	/**
	 * Uso de los filtros por defecto
	 *
	 * @var bool
	 */
	protected bool $defaultFilters = true;

	/**
	 * Soporte por defecto en el tema.
	 *
	 * @var bool
	 */
	protected bool $defaultSupport = true;

	/**
	 * Establecer las configuraciones por defecto
	 *
	 * @return $this
	 */
	public function setDefaultConfig(): static
	{
		$provider = class_exists('\Silmaril\Theme\Providers\ConfigProvider')
			? new \Silmaril\Theme\Providers\ConfigProvider()
			: new Config();

		// Archivos por defecto a cargar
		$files = ['actions', 'sidebars'];

		if ( $provider->defaultEnqueue ) {
			$files[] = 'enqueue';
		}

		if ( $provider->defaultFilters ) {
			$files[] = 'filters';
		}

		if ( $provider->defaultSupport ) {
			$files[] = 'support';
		}

		if ( $provider->useTaxonomies ) {
			$files[] = 'taxonomies';
		}

		if ( $provider->usePostTypes ) {
			$files[] = 'post_types';
		}

		Configs::create($this->load(
			array_unique($files)
		));

		return $this->takeNewActions($provider);
	}

	/**
	 * Adopta o toma nuevas acciones,
	 * según sea las configuraciones que haya en los providers
	 *
	 * @return $this
	 */
	public function takeNewActions($provider): static
	{
		// Agregar nuevo estados de soporte
		if ( $this->defaultSupport ) {
			Configs::add('actions', [
				[
					'action'   => 'after_setup_theme',
					'call'     => [Start::class, 'themeSupport'],
					'params'   => 0,
					'priority' => 10,
				]
			]);
		}

        // Agrega las taxonomies
        if( $provider->useTaxonomies && !HAS_THEME_CACHE ){
            Configs::add('actions', [
                [
                    'action'   => 'init',
                    'call'     => [\Silmaril\Core\Contents\Taxonomies::class, 'register'],
                    'params'   => 0,
                    'priority' => 8,
                ],
            ]);
        }

		// Agregar post types
		if ( $provider->usePostTypes && !HAS_THEME_CACHE ) {
			Configs::add('actions', [
				[
					'action'   => 'init',
					'call'     => [\Silmaril\Core\Contents\PostTypes::class, 'register'],
					'params'   => 0,
					'priority' => 9,
				],
			]);
		}

		return $this;
	}

	/**
	 * Establece las configuraciones del tema,
	 * y son reemplazadas o sobre/escritas
	 *
	 * @return $this
	 */
	public function setThemeConfigs(): static
	{
		if ( !class_exists('\Silmaril\Theme\Providers\ConfigProvider') ) {
			return $this;
		}

		$configProvider = new \Silmaril\Theme\Providers\ConfigProvider();

		$load = $configProvider->load(
			$configProvider->loadConfigFiles()
		);

		foreach ($load as $key => $value) {
			Configs::add($key, $value);
		}

		// Adjuntar uso de Vite
		if ( $configProvider->useVite() ) {
			// Carga en Modo Debug
			if ( WP_DEBUG ) {
				Configs::add('actions', [
					[
						'action'   => 'wp_head',
						'call'     => [Frontend::class, 'useVite'],
						'params'   => 0,
						'priority' => 100,
					]
				]);
			}

			// Carga en desarrollo
			else {
				$provider = class_exists('\Silmaril\Theme\Providers\ThemeProvider')
					? new \Silmaril\Theme\Providers\ThemeProvider()
					: new \Silmaril\Core\Providers\Theme();

				$provider->manifest();
			}
		}

		return $this;
	}

	/**
	 * Cargar todas las configuraciones
	 *
	 * @param array $files
	 * @return array
	 */
	public function load(array $files = []): array
	{
		$return = [];

		foreach ($files as $file) {
			$getFile = get_theme_file_path($this->configPath.DIRECTORY_SEPARATOR.$file.'.php');

			if ( !is_file($getFile) ) {
				Debug::addMessage('No se ha encontrado el archivo: '.$getFile, 'error');
				continue;
			}

			$return[$file] = require $getFile;
		}

		return $return;
	}

	/**
	 * Revisa la caché de configuraciones
	 *
	 * @return bool
	 */
	public function checkCache(): bool
	{
		/**
		 * Determina si existe una cache.
		 */
		define('HAS_THEME_CACHE', $this->exits());

		if ( HAS_THEME_CACHE ) {
			$this->loadCacheFiles();
		}

		return HAS_THEME_CACHE;
	}

	/**
	 * Establece todas las configuraciones,
	 * que son almacenadas en Session.
	 *
	 * @return void
	 */
	public static function setAllConfigs(): void
	{
		$self = new self;

		if ( $self->checkCache() ) {
			return;
		}

		$self
			->setDefaultConfig()
			->setThemeConfigs()
			->adjustScripts();
	}

	/**
	 * Ajustar el estado de los scripts y estilos
	 *
	 * @return $this
	 */
	public function adjustScripts(): static
	{
		if ( HAS_THEME_CACHE ) {
			return $this;
		}

		$enqueue = new Enqueue();
		$newQueues = [];
		$list = Configs::get('enqueue');
		Configs::add('all-queues', $list);

		foreach ( $list as $key => $queue ) {
			if ( count($queue) < 1 ) {
				continue;
			}

			foreach ( $queue as $q => $v ) {
				if ( !isset($v['url']) ) {
					Debug::addMessage("No existe una URL para script: {$q} de la lista de: {$key}", 'error');
					continue;
				}

				$add = [
					'key'  => TEXT_DOMAIN.'-'.$q,
					'url'  => $enqueue->getUrlFile($v['url']),
					'ver'  => $enqueue->version(
						$v['ver'] ?? '',
						is_string($v['url']) ? $v['url'] : ''
					),
					'deps' => $v['deps'] ?? [],
					'force' => $v['force'] ?? false, // Forzar inclusion del script
				];

				if ( str_contains($key, 'js') ) {
					$add['footer'] = $v['footer'] ?? false;
				}
				else {
					$add['media'] = $v['media'] ?? 'all';
				}

				$newQueues[$key][] = $add;
			}
		}

		Configs::replace('enqueue', $newQueues);

		return $this;
	}

	/**
	 * Verificar si se usa vite
	 *
	 * @return bool
	 */
	public function useVite(): bool
	{
		return $this->vite;
	}
}