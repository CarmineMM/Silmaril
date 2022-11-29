<?php

namespace Silmaril\Core\Cache;

use Silmaril\Core\Cache\Generators\Enqueue;
use Silmaril\Core\Debug;
use Silmaril\Core\Foundation;
use Silmaril\Core\Support\Collection;
use Silmaril\Core\Support\Configs;
use Silmaril\Core\Support\Str;

/**
 * Guardar en caché y recuperar archivos guardados.
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 2.0.0
 */
class Generator
{
    use \Silmaril\Core\Cache\Generators\Enqueue;
    use \Silmaril\Core\Cache\Generators\Filters;
    use \Silmaril\Core\Cache\Generators\Sidebars;
    use \Silmaril\Core\Cache\Generators\Support;
    use \Silmaril\Core\Cache\Generators\Contents;

	/**
	 * Carpeta donde se guardaran los archivos de caché
	 *
	 * @var string
	 */
	private string $folder = 'Core/Cache/storage';

	/**
	 * Lista de archivos en cache
	 *
	 * @var array|string[]
	 */
	private array $files = [
		'actions'    => 'actions.php',
		'enqueue'    => 'enqueue.php',
		'filters'    => 'filters.php',
		'sidebars'   => 'sidebars.php',
		'support'    => 'support.php',
		'taxonomies' => 'taxonomies.php',
	];

	/**
	 * Verifica la existencia de archivos en caché
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	public function exits(string $file = ''): bool
	{
		if ( $file !== '' && !is_file($this->pathFileCache($file)) ) {
			return false;
		}

		foreach ($this->files as $file) {
			if ( !is_file($this->pathFileCache($file)) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Listado de archivos a crear, guardar
	 *
	 * @param string $file
	 * @return string
	 */
	public function getFile(string $file): string
	{
		return $this->files[$file] ?? '';
	}

	/**
	 * Obtiene la ruta completa hacia un archivo
	 *
	 * @param string $file
	 * @return string
	 */
	public function pathFileCache(string $file):string
	{
		if ( empty($file) ) {
			return '';
		}

		return get_theme_file_path($this->folder.DIRECTORY_SEPARATOR.$file);
	}

    /**
     * Crear archivos de caché para las queues
     *
     * @return void
     * @throws \Exception
     */
	public static function createFilesCache(): void
	{
		if ( WP_DEBUG && !HAS_THEME_CACHE ) {
			return;
		}

		$self = new self;

		foreach (Configs::all()->toArray() as $index => $config)
		{
			$content = match ($index) {
				'actions'    => $self->createActionsAndFiltersContent($config),
				'support'    => $self->createSupportContent($config),
				'enqueue'    => $self->createEnqueueContent($config),
				'filters'    => $self->createActionsAndFiltersContent($config, true),
				'sidebars'   => $self->createSidebars($config),
                'taxonomies' => $self->createFunctionTaxonomies($config),
				default      => '',
			};

			if ( $content !== '' ) {
				$self->createFile(
					$self->pathFileCache($self->getFile($index)),
					$content,
				);
			}
		}
	}

	/**
	 * Crea un archivo
	 *
	 * @param $file
	 * @param $content
	 * @return bool
	 */
	public function createFile($file, $content): bool
	{
		if ( empty($file) ) {
			return false;
		}

		$file = fopen($file, 'w+b');

		// No se pudo crear el archivo
		if ( !$file ) {
			Debug::addMessage("Imposible crear el archivo: {$file}", 'warning');
			return false;
		}

		fwrite($file, "<?php \n");

		fwrite($file, $content);

		fflush($file);

		fclose($file);

		return true;
	}

    /**
     * Crea el contenido para las acciones pero en caché.
     *
     * @param array $config
     * @param bool $filter
     * @return string
     */
    protected function createActionsAndFiltersContent(array $config, bool $filter = false): string
    {
        $content = '';
        $exec = 'add_action';

        if ( $filter ) {
            $exec = 'add_filter';
        }

        foreach ($config as $action) {
            $call = $action['call'];

            // Llamada de una clase o function
            if ( is_array($call) ) {
                // Evitar que se guarde en caché, la ejecución de la creación de caché.
                if($call[0] === self::class && $call[1] === 'createCache') {
                    continue;
                }

                // Prevenir la carga de los debugs console
                else if ( $call[0] === Debug::class && $call[1] === 'logConsole' ) {
                    continue;
                }

                // Prevenir la carga de sidebars en vivo, y usar funciones
                else if( $call[0] === Foundation::class && $call[1] === 'registerSidebars' ) {
                    $call = $this->getNameFuncRegisterSidebars();
                }

                // Carga de scripts por funciones
                else if ( $call[0] === \Silmaril\Core\Enqueue::class && $call[1] === 'load' ) {
                    $call = $this->getNameFuncScripts();
                }

                // Carga de scripts en administrador por funciones
                else if ( $call[0] === \Silmaril\Core\Enqueue::class && $call[1] === 'loadInAdmin' ) {
                    $call = $this->getNameFuncScriptsAdmin();
                }

                // Prevenir guardado de las taxonomies
                else if ( $call[0] === \Silmaril\Core\Contents\Taxonomies::class && $call[1] === 'register' ) {
                    $call = $this->getNameFuncTaxonomies();
                }

                // Carga normal
                else {
                    $call = "[{$call[0]}::class, '{$call[1]}']";
                }
            }

            // Colocar comillas a los solo strings
            if ( is_string($call) && (!str_contains($call, '[') || !str_contains($call, ']')) ) {
                $call = "'{$call}'";
            }

            // Parámetros
            $params = 0;
            if ( isset($action['args']) ) {
                $params = $action['args'];
            }

            // Filtro o action
            $key = $action['action'] ?? '';

            if ( $filter ) {
                $key = $action['filter'] ?? '';
            }

            if ( $key ) {
                $content .= "\n{$exec}('{$key}', {$call}, {$action['priority']}, {$params});";
            }
        }

        return $content;
    }

	/**
	 * Carga los archivos de caché.
	 *
	 * @return void
	 */
	protected function loadCacheFiles(): void
	{
		foreach ($this->files as $file) {
			$file = $this->pathFileCache($file);

			if ( is_file($file) ) {
				require_once $file;
			}
		}
	}

	/**
	 * Generar dependencias
	 *
	 * @param array|string $deps
	 * @return string
	 */
	protected function generateDeps(array|string $deps): string
	{
		if ( is_array($deps) ) {
			$for = '[';
			foreach ( $deps as $dep ) {
				$for .= "'$dep',";
			}
			$for .= ']';
			$deps = $for;
		}

		return $deps;
	}
}