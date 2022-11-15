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
	 * Devuelve los registros
	 *
	 * @param array|null $columns
	 * @return Collection
	 */
	public function all(array $columns = null): Collection
	{
		$select = implode(',', is_null($columns) ? $this->select : $columns);

		$this->query = "SELECT {$select} FROM {$this->table}";

		return $this->get();
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
		if ( empty($this->query) ) {
			$this->action();
		}

		if ( $sentence !== '' ) {
			$this->query .= " WHERE {$column} {$sentenceOrDelimit} '{$sentence}'";
		}
		else {
			$this->query .= " WHERE {$column} = '{$sentenceOrDelimit}'";
		}

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
	 * @param array|null $hidden
	 * @return Collection
	 */
	public function get(array $hidden = null): Collection
	{
		global $wpdb;

		if ( WP_DEBUG ) {
			\Silmaril\Core\Debug::addMessage("Consulta: {$this->query}", 'info');
		}

		return $this->getResults($wpdb->get_results($this->query));
	}

	/**
	 * Select
	 *
	 * @param array $select
	 * @return $this
	 */
	public function select(array $select): static
	{
		$this->select = $select;
	}

	/**
	 * Action
	 *
	 * @param string $action
	 * @return void
	 */
	private function action(string $action = 'SELECT'): void
	{
		$columns = implode(', ', $this->select);
		$this->query = "$action $columns";

		if ( $action === 'SELECT' || $action === 'select' ) {
			$this->query .= " FROM {$this->table}";
		}
	}
}