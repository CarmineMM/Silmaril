<?php

namespace Silmaril\Core\Models;

use Silmaril\Core\Model;

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