<?php

namespace Silmaril\Core;

/**
 * Carga archivos externos como templates
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
class Template
{
	/**
	 * Source de donde saca los templates
	 *
	 * @var string
	 */
	private string $src = 'Theme/templates';

	/**
	 * Tipo de archivo cargado
	 *
	 * @var string
	 */
	private string $fileType = '.php';

	/**
	 * Establece el tipo de archivo
	 *
	 * @param string $fileType
	 * @return $this
	 */
	public function setFileType( string $fileType ): static
	{
		$this->fileType = '.'.trim($fileType, '.');
		return $this;
	}

	/**
	 * Establece el source
	 *
	 * @param string $src
	 * @return $this
	 */
	public function setSrc( string $src ): static
	{
		$this->src = str_replace('.', DIRECTORY_SEPARATOR, $src);
		return $this;
	}

	/**
	 * Renderiza una vista
	 *
	 * @param string $view
	 * @param array $data
	 *
	 * @return string
	 */
	public function render( string $view, array $data = [] ): string
	{
		return $this->renderElse($view, '', $data);
	}

	/**
	 * Renderizado de forma condicional
	 *
	 * @param string $view
	 * @param string $viewElse
	 * @param array $data
	 * @return string
	 */
	public function renderElse(string $view, string $viewElse = '', array $data = []): string
	{
		$view = str_replace('.', DIRECTORY_SEPARATOR, $view);
		$file = get_theme_file_path($this->src . DIRECTORY_SEPARATOR . $view . $this->fileType);

		if ( !is_file($file) ) {
			if ( $viewElse === '' ) {
				Debug::addMessage("Vista no encontrada: {$file}", 'error');
				return "<h1 style='font-size: 2rem'>Vista no encontrada: {$file}</h1>";
			}

			$file = get_theme_file_path(str_replace('.', DIRECTORY_SEPARATOR, $viewElse) . $this->fileType);
		}

		if ( $this->fileType === '.html' ) {
			echo file_get_contents($file);
			return '';
		}

		ob_start();
		extract($data);
		require_once $file;
		return ob_get_clean();
	}
}