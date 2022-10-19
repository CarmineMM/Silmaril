<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Debug;
use Silmaril\Core\Support\Collection;
use Silmaril\Core\Support\Configs;
use Silmaril\Core\Support\Str;

class Vite
{
	/**
	 * Servidor para Vite
	 *
	 * @var string
	 */
	protected string $viteServer = 'http://localhost:8081/';

	/**
	 * Script principal de Vite
	 *
	 * @var string
	 */
	protected string $viteScript = 'main.js';

	/**
	 * Vite client
	 *
	 * @see https://vitejs.dev/guide/backend-integration.html#backend-integration
	 * @var string
	 */
	protected string $viteClient = '@vite/client';

	/**
	 * Dirección hacia el manifest de Vite.
	 *
	 * @var string
	 */
	protected string $viteManifest = 'assets';

	/**
	 * Ajustar para que el CSS principal cargue en todos lados
	 *
	 * @var bool
	 */
	protected bool $loadMainCssInAll = true;

	/**
	 * Obtener la url al bundle en cache de vite
	 *
	 * @return string
	 */
	public function getViteScript(): string
	{
		return trim($this->viteServer, '/') . '/' . trim($this->viteScript, '/');
	}

	/**
	 * Obtener la url al cliente de vite
	 *
	 * @return string
	 */
	public function getViteClient(): string
	{
		return trim($this->viteServer, '/') . '/' . trim($this->viteClient, '/');
	}

	/**
	 * Carga el manifest
	 *
	 * @return void
	 */
	public function manifest(): void
	{
		$this->viteManifest = rtrim($this->viteManifest, '/');

		$manifest = "{$this->viteManifest}/manifest.json";
		$file = file_get_contents(get_theme_file_path($manifest));

		if ( !$file ) {
			Debug::addMessage("Manifest.json no encontrado en: {$manifest}", 'error');
			return;
		}

		$this->loadFilesFromManifest(
			new Collection(json_decode($file))
		);
	}

	/**
	 * Cargar los archivos del manifiesto
	 *
	 * @param Collection $manifest
	 * @return void
	 */
	private function loadFilesFromManifest(Collection $manifest): void
	{
		$load = new Collection([]);

		$manifest->each(function ($el, $key) use ($load) {
			$key = new Str($key);

			// Cargar
			if ( $key->contains('.css') ) {
				if ( $key->equals('main.css') && $this->loadMainCssInAll ) {
					$load->add('all-css', [
						'main-css' => [
							'url' => $this->viteManifest .'/'. $el->file,
						],
					]);
					return;
				}

				$load->add('css', [
					$key->replace('.', '-')->toString() => [
						'url' => $this->viteManifest .'/'. $el->file,
					],
				]);

				return;
			}

			// Cargar Scripts
			$load->add('js', [
				$key->replace('.', '-')->toString() => [
					'url'  => $this->viteManifest .'/'. $el->file,
					'deps' => ['jquery'],
				],
			]);
		});

		$load->combineRecursive(Configs::get('enqueue'));

		Configs::replace('enqueue', $load->toArray());
	}
}
