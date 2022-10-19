<?php

/**
 * Debug en la consola
 *
 * @param string|array $msg
 *
 * @return void
 */
function console_log(...$msg): void
{
	foreach ($msg as $m) {
		\Silmaril\Core\Debug::addMessage($m);
	}
}

/**
 * Debug en la consola, info
 *
 * @param string|array $msg
 *
 * @return void
 */
function console_info(...$msg): void
{
	foreach ($msg as $m) {
		\Silmaril\Core\Debug::addMessage($m, 'info');
	}
}

/**
 * Debug en la consola, info
 *
 * @param string|array $msg
 *
 * @return void
 */
function console_error(...$msg): void
{
	foreach ($msg as $m) {
		\Silmaril\Core\Debug::addMessage($m, 'error');
	}
}

/**
 * Debug en la consola, info
 *
 * @param string|array $msg
 *
 * @return void
 */
function console_warning(...$msg): void
{
	foreach ($msg as $m) {
		\Silmaril\Core\Debug::addMessage($m, 'warning');
	}
}

/**
 * Debug una variable u objeto
 *
 * @param mixed $debug
 * @return void
 */
function debug(...$debug): void
{
	\Silmaril\Core\Debug::show(...$debug);
}

/**
 * Convertir a collection
 *
 * @param mixed $i
 * @return \Silmaril\Core\Support\Collection
 */
function collect(mixed $i): \Silmaril\Core\Support\Collection
{
	return new \Silmaril\Core\Support\Collection($i);
}

/**
 * Obtiene un template part
 *
 * @param string $file Usando Dot notation
 * @return void
 */
function template_part(string $file): void
{
	$file = str_replace('.', '/', $file);
	get_template_part("template-parts/{$file}");
}


if ( !function_exists('view') )
{
	/**
	 * Renderizar una vista
	 *
	 * @param string $view
	 * @param array $data
	 * @return string
	 */
	function view(string $view, array $data = []): string
	{
		return (new \Silmaril\Core\Template())->render($view, $data);
	}
}

function getUriTheme(string $url): string
{
	return (new \Silmaril\Core\Enqueue())->getUrlFile($url);
}

if ( !function_exists('str') )
{
	/**
	 * Helper de instancia para los Strings
	 *
	 * @param string $string
	 * @return \Silmaril\Core\Support\Str
	 */
	function str(string $string): \Silmaril\Core\Support\Str
	{
		return new \Silmaril\Core\Support\Str($string);
	}
}
