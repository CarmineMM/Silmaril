<?php
/**
 * Actions enlazados la ejecución de Wordpress
 *
 * Se debe delimitar por POO, donde se indica la clase y el método.
 * También, se puede colocar la función objetiva con ['fn' => 'Function objetiva'].
 * Se le dara prioridad a las clases y métodos.
 *
 */

return [
	[
		'action'   => 'wp_head',
		'call'     => [\Silmaril\Theme\Hooks\WP_Head::class, 'run'],
		'priority' => 5,
		'args'     => 0,
	],
];
