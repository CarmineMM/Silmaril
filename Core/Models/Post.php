<?php

namespace Silmaril\Core\Models;

use Silmaril\Core\Model;

/**
 * Modelo que se comunica con la tabla wp_posts
 * siendo "wp_" el prefix
 *
 * @author Carmine Maggio
 * @version 1.1.0
 * @package Silmaril Theme
 */
class Post extends Model
{
	/**
	 * Casts
	 *
	 * @var array|string[]
	 */
	protected array $casts = [
		'image' => 'serialize'
	];
}