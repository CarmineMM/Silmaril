<?php

use Silmaril\Theme\Providers\HooksProvider;
use Silmaril\Core\Enqueue;

/**
 * Actions enlazados la ejecución de Wordpress
 *
 * Se debe delimitar por POO, donde se indica la clase y el método.
 * También, se puede colocar la función objetiva con ['call' => 'Function objetiva'].
 * Se le dara prioridad a las clases y métodos.
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
return [
	/**
	 * Al inicio de la ejecución de wordpress
	 *
	 * @see https://developer.wordpress.org/reference/hooks/init/
	 */
	[
		'action'   => 'init',
		'call'     => [HooksProvider::class, 'init'],
		'priority' => 10,
		'args'     => 1,
	],

	/**
	* Activación del tema
	*
	* @see https://developer.wordpress.org/reference/hooks/after_switch_theme/
	*/
	[
		'action'   => 'after_switch_theme',
		'call'     => [HooksProvider::class, 'activateTheme'],
		'priority' => 10,
		'args'     => 1,
	],

	/**
	 * Ejecución durante la carga, después de iniciar el tema.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/after_setup_theme/
	 */
	[
		'action'   => 'after_setup_theme',
		'call'     => [HooksProvider::class, 'run'],
		'priority' => 10,
		'args'     => 1,
	],

	/**
	 * Cargar Hojas de estilos.
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_scripts/
	 */
	[
		'action'   => 'wp_enqueue_scripts',
		'call'     => [Enqueue::class, 'load'],
		'priority' => 10,
		'args'     => 0,
	],

	/**
	 * Cargar Hojas de estilos en la parte administrativa
	 *
	 * @see https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
	 */
	[
		'action'   => 'admin_enqueue_scripts',
		'call'     => [Enqueue::class, 'loadInAdmin'],
		'priority' => 10,
		'args'     => 0,
	],

	/**
	 * Muestra el debug console.
	 * Doc. del Hook:
	 *
	 * @see https://developer.wordpress.org/reference/hooks/wp_footer/
	 */
	[
		'action'   => 'wp_footer',
		'call'     => [\Silmaril\Core\Debug::class, 'logConsole'],
		'priority' => 9,
		'args'     => 0,
	],

	/**
	 * Final de la ejecución de wordpress,
	 * crear la caché del tema.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/shutdown/
	 */
	[
		'action'   => 'shutdown',
		'call'     => [\Silmaril\Core\Cache\Generator::class, 'createFilesCache' ],
		'priority' => 100,
		'args'     => 0,
	],
];