<?php

namespace Silmaril\Core;

/**
 * Respuestas para la API de WordPress
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
final class Response
{
	/**
	 * Respuesta afirmativa para las APIs WordPress
	 *
	 * @param mixed $response
	 * @param int $code
	 *
	 * @return void
	 */
	public static function success(mixed $response, int $code = 200): void
	{
		header('Content-Type: application/json');
		http_response_code($code);
		echo json_encode($response);
		die();
	}

	/**
	 * Respuesta de errores para la API de WordPress
	 *
	 * @param $error
	 * @param int $code
	 *
	 * @return void
	 */
	public static function error($error, int $code = 400): void
	{
		header('Content-Type: application/json');
		http_response_code($code);
		echo json_encode($error);
		die();
	}
}