<?php

namespace Silmaril\Core\Support;

use Exception;

/**
 * Clase para ayudar la conversion de los strings
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 1.0.0
 */
class Str
{
	/**
	 * Encode
	 *
	 * @var string
	 */
	private string $encode = 'UTF-8';

	/**
	 * Construct.
	 *
	 * @param string $str
	 */
	public function __construct(
		private string $str
	) {
		//
	}

	/**
	 * Instancia rápida de la clase.
	 *
	 * @param string $str
	 * @return Str
	 */
	public static function of(string $str): Str
	{
		return new self($str);
	}

	/**
	 * Identifica si contiene uno de sus elementos.
	 *
	 * @param string|array $needles
	 * @param bool $ignoreCase
	 * @return bool
	 */
	public function contains(string|array $needles, bool $ignoreCase = false): bool
	{
		$haystack = $this->str;

		if ($ignoreCase) {
			$haystack = mb_strtolower($haystack);
			$needles = array_map('mb_strtolower', (array) $needles);
		}

		foreach ((array) $needles as $needle) {
			if ($needles !== '' && str_contains($haystack, $needle)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Longitud de caracteres.
	 *
	 * @param $encoding
	 * @return int
	 */
	public function length($encoding = null): int
	{
		return mb_strlen($this->str, $encoding);
	}

	/**
	 * Minuscules.
	 *
	 * @return Str
	 */
	public function lower(): Str
	{
		$this->str = mb_strtolower($this->str, $this->encode);

		return $this;
	}

	/**
	 * Mayúsculas.
	 *
	 * @return Str
	 */
	public function upper(): Str
	{
		$this->str = mb_strtoupper($this->str, $this->encode);

		return $this;
	}

	/**
	 * Convierte el string, en un case title.
	 *
	 * @return Str
	 */
	public function title(): Str
	{
		$this->str = mb_convert_case($this->str, MB_CASE_TITLE, $this->encode);

		return $this;
	}

	/**
	 * Convierte el str a un slug
	 *
	 * @param string $separator
	 * @param string $language
	 * @return Str
	 */
	public function slug(string $separator = '-', string $language = 'en'): Str
	{
		// Convert all dashes/underscores into separator
		$flip = $separator === '-' ? '_' : '-';

		$this->str = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $this->str);

		// Replace @ with the word 'at'
		$this->str = str_replace('@', $separator . 'at' . $separator, $this->str);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$this->str = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', $this->lower($this->str)->toString());

		// Replace all separator characters and whitespace by a single separator
		$this->str = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $this->str);

		$this->str = trim($this->str, $separator);

		return $this;
	}

    /**
     * Lleva a slug y devuelve el string
     *
     * @param string $separator
     * @param string $language
     * @return string
     */
    public function toSlug(string $separator = '-', string $language = 'en'): string
    {
        return $this->slug($separator, $language)->toString();
    }

	/**
	 * Reemplazar un elemento por otro
	 * 
	 * @param array|string $search
	 * @param array|string $replace
	 * @return $this
	 */
	public function replace(array|string $search, array|string $replace): static
	{
		$this->str = str_replace($search, $replace, $this->str);

		return $this;
	}

	/**
	 * Pluralizar una palabra
	 *
	 * @param string $lang
	 *
	 * @return Str
	 */
	public function plural(string $lang = 'es'): Str
	{
		$ending = 's';

		if ( $lang === 'es' ) {
			$endingWith_ES = [
				'actor', 'director', 'pais', 'mujer', 'árbol', 'arbol', 'español', 'marsupial',
				'editor', 'autor', 'color', 'reloj', 'pais', 'país'
			];

			$endingWith_CES_and_delete_last_character = [
				'pez', 'automotriz', 'altavoz', 'vez', 'raíz', 'veloz', 'voz', 'interfaz', 'lápiz'
			];

			$wordsWithoutPlural = [
				'ciempiés', 'buscapiés', 'salud', 'caos', 'sed', 'víveres', 'enseres', 'honorarios', 'tesis',
				'caries', 'tórax', 'crisis',
			];

			if ( in_array(strtolower($this->str), $endingWith_ES) ) {
				$ending = 'es';
			}
			else if ( in_array(strtolower($this->str), $endingWith_CES_and_delete_last_character) ) {
				$ending = 'ces';
				$this->str = substr($this->str, 0, -1);
			}
			else if( in_array(strtolower($this->str), $wordsWithoutPlural) ) {
				$ending = '';
			}
		}


		$this->str = $this->str . $ending;
		return $this;
	}

	/**
	 * Agrega una o multiples palabras al inicio
	 *
	 * @param string ...$prepend
	 * @return Str
	 */
	public function prepend(string ...$prepend): Str
	{
		foreach ($prepend as $p) {
			$this->str = $p.$this->str;
		}

		return $this;
	}

	/**
	 * Agregar una o multiples palabras al final
	 *
	 * @param string ...$append
	 * @return $this
	 */
	public function append(string ...$append): Str
	{
		foreach ($append as $p) {
			$this->str .= $p;
		}

		return $this;
	}

	/**
	 * Comprobar que sea igual a...
	 *
	 * @param string $is
	 * @param bool $ignoreCase
	 * @return bool
	 */
	public function equals(string $is, bool $ignoreCase = false): bool
	{
		if ( $ignoreCase ) {
			return strtolower($this->str) === strtolower($is);
		}

		return $this->str === $is;
	}

	/**
	 * Obtiene el resultado, o el original string
	 *
	 * @return string
	 */
	public function toString(): string
	{
		return $this->str;
	}

	/**
	 * Devuelve el float o un 0 en caso de no poder convertir
	 *
	 * @return float
	 */
	public function toFloat(): float
	{
		try {
			return (float) $this->str;
		} catch (Exception $e) {
			return 0;
		}
	}
}
