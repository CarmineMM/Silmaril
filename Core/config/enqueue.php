<?php

/**
 * Lista de archivos css o scripts, en el tema
 *
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
return [
	/*
	|--------------------------------------------------------------------------
	| Configuraciones para los Styles
	|--------------------------------------------------------------------------
	|
	| Las llaves que debe contener los css, son los siguientes:
	| 'url' => 'URL de destino, puede ser un array, donde el primer elemento,
	| Sera de desarrollo y el segundo el de production',
	| 'ver' => 'Version',
	| 'deps' => ['Dependencias', 'marcados por un array'],
	| 'dev' => false // Solo para desarrollo, por defecto es falso
	| 'media' => 'Indica en que tamaños se debe ver' Default: 'all'
	|
	*/
	'css' => [
		'theme-style' => [ 'url' => get_stylesheet_uri() ],
	],

	/*
	|--------------------------------------------------------------------------
	| Configuraciones para los Scripts
	|--------------------------------------------------------------------------
	|
	| Las llaves que debe contener los js, son los siguientes:
	|'url' => 'URL de destino, puede ser un array, donde el primer elemento,
	| Sera de desarrollo y el segundo el de production',
	| 'ver' => 'Version',
	| 'deps' => ['Dependencias', 'marcados por un array'],
	| 'dev' => false // Solo para desarrollo, por defecto es falso
	| 'footer' => false // Delimita si carga en el footer
	|
	*/
	'js'  => [],

	/*
	|--------------------------------------------------------------------------
	| Estilos y scripts cargados únicamente en el admin
	|--------------------------------------------------------------------------
	|
	*/
	'admin-css' => [],

	'admin-js'  => [],

	/*
	|--------------------------------------------------------------------------
	| Estilos y scripts cargados únicamente depuración
	|--------------------------------------------------------------------------
	|
	| Los estilos cargados para depuración, también cargarán en el admin.
	|
	*/
	'debug-css' => [
		'debug' => [
			'url'   => 'Core/assets/css/debug.css',
			'force' => false,
		],
	],

	'debug-js'  => [
		'debug' => [
			'url'   => 'Core/assets/js/debug.js',
			'deps'  => ['jquery'],
			'force' => false,
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Estilos y scripts cargados en frontend y en el admin
	|--------------------------------------------------------------------------
	|
	*/
	'all-css' => [],
	'all-js'  => [],
];