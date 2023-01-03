<?php

namespace Silmaril\Core;

use Silmaril\Core\Support\Collection;
use Silmaril\Core\Support\Str;

class Model
{
	/**
	 * Tabla
	 *
	 * @var string
	 */
	protected string $table = '';

	/**
	 * Columnas devueltas
	 *
	 * @var array|string[]
	 */
	protected array $select = ['*'];

	/**
	 * Ocultar ciertos campos
	 *
	 * @var array
	 */
	protected array $hidden = [];

	/**
	 * Casts
	 *
	 * @var array
	 */
	protected array $casts = [];

	/**
	 * Consulta preparada
	 *
	 * @var string
	 */
	private string $query = '';

	/**
	 * Ocultar ciertos campos
	 *
	 * @var bool
	 */
	private bool $hiddenColumn = true;

	/**
	 * Construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		global $wpdb;

		if ( !$this->table ) {
			$table = explode('\\', get_called_class());
			$this->table = (new Str(array_pop($table)))
				->lower()
				->prepend($wpdb->prefix)
				->plural()
				->toString();
		}
	}

	/**
	 * Obtiene la tabla del modelo
	 *
	 * @return string
	 */
	public function getTable(): string
	{
		return $this->table;
	}

	/**
	 * Devuelve los registros
	 *
	 * @param array|string|null $columns
	 * @return Collection
	 */
	public function all(array|string $columns = null): Collection
	{
		$this->query = "SELECT %COLUMNS% FROM {$this->table}";

		return $this->get($columns ?? '');
	}

	/**
	 * Ocultar obtener resultados
	 *
	 * @param array $results
	 * @param array|null $hidden
	 * @return Collection
	 */
	private function getResults(array $results, array $hidden = null): Collection
	{
		$hidden = is_null($hidden) ? $this->hidden : $hidden;
		$collect = new Collection($results);
		$casts = $this->casts;

		$collect->map(function ($el) use ($hidden, $casts) {
			$el = (array) $el;

			// Hidden
			if ( $this->hiddenColumn ) {
				foreach ($hidden as $h) {
					unset($el[$h]);
				}
			}

			// Casts
			foreach ($casts as $key => $cast) {
				if ( isset($el[$key]) && is_string($cast) ) {
					$el[$key] = $this->castWithStrings($cast, $el[$key]);
				}
			}

			return $el;
		});

		return $collect;
	}

	/**
	 * No Ocultar campos
	 *
	 * @return $this
	 */
	public function noHidden():static
	{
		$this->hiddenColumn = false;
		return $this;
	}

	/**
	 * Condiciona
	 *
	 * @param $column
	 * @param $sentenceOrDelimit
	 * @param string $sentence
	 * @return $this
	 */
	public function where($column, $sentenceOrDelimit, string $sentence = ''): static
	{
		$construct = str_contains($this->query, 'WHERE') ? 'AND' : 'WHERE';
		$this->query .= $this->sentenceConstructor($column, $sentenceOrDelimit, $sentence, $construct);

		return $this;
	}

	/**
	 * Constructor de sentencia
	 *
	 * @param $column
	 * @param $sentenceOrDelimit
	 * @param string $sentence
	 * @param string $construct
	 * @return string
	 */
	private function sentenceConstructor($column, $sentenceOrDelimit, string $sentence = '', string $construct = 'WHERE'): string
	{
		if ( empty($this->query) ) {
			$this->action();
		}

		if ( !str_contains($column, '.') ) {
			$column = "{$this->table}.{$column}";
		}

		if ( $sentence !== '' ) {
			return " {$construct} {$column} {$sentenceOrDelimit} {$sentence}";
		}

		return " {$construct} {$column} = '{$sentenceOrDelimit}'";
	}

	/**
	 * Sentencia orWhere, pero que agrega un Where al principio si no lo hay
	 *
	 * @param $column
	 * @param $sentenceOrDelimit
	 * @param string $sentence
	 * @return $this
	 */
	public function orWhere($column, $sentenceOrDelimit, string $sentence = ''): static
	{
		if ( !str_contains($this->query, 'WHERE') ) {
			return $this->where($column, $sentenceOrDelimit, $sentence);
		}

		$this->query .= $this->sentenceConstructor($column, $sentenceOrDelimit, $sentence, 'OR');
		return $this;
	}

	/**
	 * Limitar
	 *
	 * @param int $limit
	 * @param int|null $orLimit
	 * @return Model
	 */
	public function limit(int $limit, int $orLimit = null): static
	{
		if ( is_null($orLimit) && !str_contains($this->query, 'LIMIT') ) {
			$this->query .= " LIMIT {$limit}";
		}
		else if( !str_contains($this->query, 'LIMIT') ) {
			$this->query .= " LIMIT {$limit}, {$orLimit}";
		}

		return $this;
	}

	/**
	 * Primer elemento encontrado
	 *
	 * @return Collection
	 */
	public function first(): Collection
	{
		return $this->limit(1)->get()->first();
	}

	/**
	 * Cast de valores con casts predeterminados
	 *
	 * @param $key
	 * @param $value
	 * @return mixed
	 * @throws \Exception
	 */
	private function castWithStrings($key, $value): mixed
	{
		$value = match ($key) {
			'collection' => new Collection(json_decode($value)),
			'date'       => new \DateTime($value),
			'boolean'    => (bool) $value,
			'object'     => json_decode($value),
			'serialize'  => unserialize($value),
			default      => $value,
		};

		if ( str_contains($key, 'datetime') ) {
			$key = str_replace('datetime:', '', $key);
			$value = (new \DateTime($value))->format($key);
		}

		return $value;
	}

	/**
	 * Ejecuta la consulta
	 *
	 * @param array|string $select
	 * @return Collection
	 */
	public function get(array|string $select = ''): Collection
	{
		global $wpdb;

		if ( $select !== '' ) {
			$this->select($select);
		}

		// Reemplazar estructuras condicionales
		$this->query = str_replace('%COLUMNS%', implode(', ', $this->select), $this->query);
		$this->query = str_replace('%JOINS%', '', $this->query);

		if ( WP_DEBUG ) {
			\Silmaril\Core\Debug::addMessage("Consulta: {$this->query}", 'info');
		}

		return $this->getResults($wpdb->get_results($this->query));
	}

	/**
	 * Select
	 *
	 * @param array|string $select
	 * @return $this
	 */
	public function select(array|string $select): static
	{
		if ( is_string($select) ){
			$select = [$select];
		}

		$this->select = $select;

		return $this;
	}

	/**
	 * Ordenar por...
	 *
	 * @param string $column
	 * @param string $order
	 * @return Model
	 */
	public function orderBy(string $column, string $order = 'ASC'): static
	{
		$this->query .= " ORDER BY {$column} {$order}";

		return $this;
	}

	/**
	 * Inserta un join en la consulta
	 *
	 * @param $column
	 * @param $sentence
	 * @param string $type
	 *
	 * @return $this
	 */
	public function join($column, $sentence, string $type = 'INNER'): static
	{
		if ( $this->query === '' ) {
			$this->action();
		}

		$join = "{$type} JOIN {$column} ON {$sentence}";
		$this->query = str_replace("%JOINS%", "{$join} %JOINS%", $this->query);
		return $this;
	}

	/**
	 * Action
	 *
	 * @param string $action
	 * @return void
	 */
	private function action(string $action = 'SELECT'): void
	{
		$this->query = "$action %COLUMNS%";

		if ( $action === 'SELECT' || $action === 'select' ) {
			$this->query .= " FROM {$this->table} %JOINS%";
		}
	}
}