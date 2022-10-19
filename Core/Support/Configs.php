<?php

namespace Silmaril\Core\Support;

class Configs
{
	/**
	 * Llave de la session
	 */
	const key = 'config';

	/**
	 * Todas las configuraciones
	 *
	 * @param array $configs
	 * @return array
	 */
	public static function create(array $configs = []): array
	{
		return $_SESSION[self::key] = [
			...$_SESSION[self::key] ?? [],
			...$configs
		];
	}

	/**
	 * Agrega nuevas configuraciones
	 *
	 * @param $key
	 * @param $value
	 * @return array
	 */
	public static function add($key, $value): array
	{
		$default = static::all()->toArray();

		if ( array_key_exists($key, $default) ) {
			if ( $key === 'enqueue' ) {
				$default[$key] = array_merge_recursive($default[$key], $value);
			}else {
				$default[$key] = array_merge($default[$key], $value);
			}
		}
		else {
			$default[$key] = $value;
		}

		return static::create($default);
	}

	/**
	 * Reemplaza un valor de configuración
	 *
	 * @param $key
	 * @param $values
	 * @return array
	 */
	public static function replace($key, $values): array
	{
		return $_SESSION[static::key][$key] = $values;
	}

	/**
	 * Obtiene una configuration
	 *
	 * @param $key
	 * @return array
	 */
	public static function get($key): array
	{
		return static::all()->get($key, []);
	}

	/**
	 * Obtiene todas las configuraciones
	 *
	 * @return Collection
	 */
	public static function all(): Collection
	{
		return new Collection($_SESSION[self::key] ?? []);
	}
}