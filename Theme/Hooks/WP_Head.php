<?php

namespace Silmaril\Theme\Hooks;

class WP_Head
{
	/**
	 * Ejecutar en el wp head
	 *
	 * @return void
	 */
	public static function run(): void
	{
		$self = new self();

		$self->insertGoogleFontsApi();
	}

	/**
	 * Insertar los links para google fonts api
	 *
	 * @return $this
	 */
	public function insertGoogleFontsApi(): static
	{
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
		return $this;
	}
}