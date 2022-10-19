<?php

namespace Silmaril\Core;

use Silmaril\Core\Security\Checker;

/**
 * Debug en la aplicación
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
class Debug
{
	/**
	 * Llave para depuración
	 */
	const key = 'debug';

	/**
	 * Todas las configuraciones
	 *
	 * @return array
	 */
	public static function all(): array
	{
		return $_SESSION[static::key] ?? [];
	}

	/**
	 * Agregar mensaje a la depuración
	 *
	 * @param string|array $msg
	 * @param string $type - log, error, info, warning
	 * @return array
	 */
	public static function addMessage(string|array $msg, string $type = 'log'): array
	{
		$_SESSION[static::key][] = [
			'msg'  => $msg,
			'type' => $type
		];

		return $_SESSION[static::key];
	}

	/**
	 * Muestra un log console
	 *
	 * @return string
	 */
	public static function logConsole(): string
	{
		// Solo disponible para el modo depuración y cuando se está registrado.
		if ( !WP_DEBUG || !is_user_logged_in() ) {
			return '';
		}

		Checker::checkAuthor();

		echo (new Template())->setSrc('Core.templates')->render('debug-console');
		return '';
	}

	/**
	 * Debug una variable u objeto
	 *
	 * @param mixed $show
	 * @return void
	 */
	public static function show(mixed ...$show): void
	{
		echo '<pre style="background-color: #eee; padding: 0.8rem; color: #333">';
		foreach ($show as $s) {
			var_dump($s);
		}
		echo '</pre>';
	}
}