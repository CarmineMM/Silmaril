<?php

namespace Silmaril\Core;

use Silmaril\Core\Support\Collection;
use Silmaril\Core\Support\Configs;
use Silmaril\Core\Support\Str;

/**
 * Guardar en caché y recuperar archivos guardados.
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 1.2.0
 */
class Cache
{
	/**
	 * Carpeta donde se guardaran los archivos de caché
	 *
	 * @var string
	 */
	private string $folder = 'Core/Cache';

	/**
	 * Lista de archivos en cache
	 *
	 * @var array|string[]
	 */
	private array $files = [
		'enqueue'  => 'enqueue.php',
		'support'  => 'support.php',
		'actions'  => 'actions.php',
		'filters'  => 'filters.php',
		'sidebars' => 'sidebars.php',
	];

	/**
	 * Nombre de las funciones para los scripts y estilos,
	 * en el panel y cargadas globalmente.
	 *
	 * @var string
	 */
	private string $funcStylesScripts = 'enqueue_styles_scripts';
	private string $funcAdminStylesScripts = 'enqueue_styles_scripts_admin';
	private string $funcRegisterSidebars = 'register_sidebars';

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
	 * Nombre de la función en para la carga de scripts y estilos
	 *
	 * @return string
	 */
	public function getNameFuncScripts(): string
	{
		return TEXT_DOMAIN .'_'. $this->funcStylesScripts;
	}

	/**
	 * Nombre de la función en para la carga de scripts y estilos
	 *
	 * @return string
	 */
	public function getNameFuncScriptsAdmin(): string
	{
		return TEXT_DOMAIN .'_'. $this->funcAdminStylesScripts;
	}

	/**
	 * Función para el registro de sidebars
	 *
	 * @return string
	 */
	public function getNameFuncRegisterSidebars(): string
	{
		return TEXT_DOMAIN .'_'. $this->funcRegisterSidebars;
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
	 */
	public static function createCache(): void
	{
		if ( WP_DEBUG && !HAS_THEME_CACHE ) {
			return;
		}

		$self = new self;

		foreach (Configs::all()->toArray() as $index => $config)
		{
			$content = match ($index) {
				'support'  => $self->createSupportContent($config),
				'actions'  => $self->createActionsAndFiltersContent($config),
				'enqueue'  => $self->createEnqueueContent($config),
				'filters'  => $self->createActionsAndFiltersContent($config, true),
				'sidebars' => $self->createSidebars($config),
				default   => '',
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
	 * Crea el contenido para el archivo de soportes del tema
	 *
	 * @param array $config
	 * @return string
	 */
	public function createSupportContent(array $config): string
	{
		$content = '';

		foreach ( $config as $support => $opt ) {
			if ( $opt === false ) {
				continue;
			}

			if ( is_bool($opt) ) {
				$content .= "\nadd_theme_support('{$support}');";
				continue;
			}

			if ( is_array($opt) ) {
				$array = '[';
				foreach ($opt as $i) {
					$array .= "'{$i}',";
				}
				$array .= ']';
				$content .= "\nadd_theme_support('{$support}', {$array});";
			}
		}

		return $content;
	}

	/**
	 * Crea el contenido para las acciones pero en caché.
	 *
	 * @param array $config
	 * @param bool $filter
	 * @return string
	 */
	public function createActionsAndFiltersContent(array $config, bool $filter = false): string
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
				else if ( $call[0] === Enqueue::class && $call[1] === 'load' ) {
					$call = $this->getNameFuncScripts();
				}

				// Carga de scripts en administrador por funciones
				else if ( $call[0] === Enqueue::class && $call[1] === 'loadInAdmin' ) {
					$call = $this->getNameFuncScriptsAdmin();
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
	 * Crea el contenido para las queues
	 *
	 * @param array $config
	 * @return string
	 */
	public function createEnqueueContent(array $config): string
	{
		$funcScripts = "\nfunction {$this->getNameFuncScripts()}(): void {";
		$funcScriptsAdmin = "\nfunction {$this->getNameFuncScriptsAdmin()}(): void {";

		foreach ( $config as $type => $queues )
		{
			$type = Str::of($type);

			// No guardar las de debug
			if ( $type->contains('debug') ){
				continue;
			}

			foreach ($queues as $key => $queue) {
				// Los cargados en el admin y en all
				if ( $type->contains(['all', 'admin']) ) {
					$funcScriptsAdmin .= $this->extractQueue($queue, $type->replace(['admin-', 'all-'], '')->toString());
				}

				// Cargados en el frontend y en all, menos en el admin
				if ( !$type->contains('admin') ) {
					$funcScripts .= $this->extractQueue($queue, $type->replace(['all-'], '')->toString());
				}
			}
		}

		$funcScripts .= "\n}";
		$funcScriptsAdmin .= "\n}";

		return $funcScriptsAdmin.$funcScripts;
	}

	/**
	 * Extrae el queue
	 *
	 * @param $queue
	 * @param string $file
	 * @return string
	 */
	private function extractQueue($queue, string $file): string
	{
		// Dependencias
		$deps = $queue['deps'];
		if ( is_array($deps) ) {
			$for = '[';
			foreach ( $deps as $dep ) {
				$for .= "'$dep',";
			}
			$for .= ']';
			$deps = $for;
		}

		// Footer
		$footer = 'false';
		if ( isset($q['footer']) ){
			$footer = $q['footer'] ? 'true' : 'false';
		}

		return match ($file) {
			'js'    => "\n\twp_enqueue_script('{$queue['key']}', '{$queue['url']}', {$deps}, '{$queue['ver']}', {$footer});",
			'css'   => "\n\twp_enqueue_style('{$queue['key']}', '{$queue['url']}', {$deps}, '{$queue['ver']}', '{$queue['media']}');",
			default => '',
		};
	}

	/**
	 *
	 *
	 * @param array $sidebars
	 * @return string
	 */
	public function createSidebars(array $sidebars): string
	{
		$func = "\nfunction {$this->getNameFuncRegisterSidebars()}(): void {";

		foreach ($sidebars as $sidebar) {
			$sidebar = new Collection($sidebar);

			$showInRest = $sidebar->get('show_in_rest', false) ? 'true' : 'false';

			$func .= "\n\tregister_sidebar([";
			$func .= "'id' => '". $sidebar->get('id') ."', ";
			$func .= "'name' => '". $sidebar->get('name') ."', ";
			$func .= "'description' => '". $sidebar->get('description', '') ."', ";
			$func .= "'class' => '". $sidebar->get('class', '') ."', ";
			$func .= "'before_widget' => '". $sidebar->get('before_widget', '<li id="%1$s" class="widget %2$s">') ."', ";
			$func .= "'after_widget' => '". $sidebar->get('after_widget', '</li>') ."', ";
			$func .= "'before_title' => '". $sidebar->get('before_title', '<h2 class="widgettitle">') ."', ";
			$func .= "'after_title' => '". $sidebar->get('after_title', '</h2>') ."', ";
			$func .= "'before_sidebar' => '". $sidebar->get('before_sidebar', '') ."', ";
			$func .= "'after_sidebar' => '". $sidebar->get('after_sidebar', '') ."', ";
			$func .= "'show_in_rest' => {$showInRest}";
			$func .= "]);";
		}

		$func .= "\n}";

		return $func;
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
}