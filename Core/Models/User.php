<?php

namespace Silmaril\Core\Models;

use Silmaril\Core\Model;

/**
 * Modelo que se comunica con la tabla wp_users
 * siendo "wp_" el prefix
 *
 * @author Carmine Maggio
 * @version 1.0.0
 * @package Silmaril Theme
 */
class User extends Model
{
	/**
	 * Hidden
	 *
	 * @var array|string[]
	 */
	protected array $hidden = [
		'user_pass',
	];

	/**
	 * Casts
	 *
	 * @var array|string[]
	 */
	protected array $casts = [
		'user_registered' => 'date',
	];
}