<?php

namespace Silmaril\Core\Support;

/**
 * Collections, una pequeña imitación a Collection de Laravel
 * 
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 1.1.0
 */
class Collection
{
    /**
     * Original
     *
     * @var array
     */
    private array $original = [];

    /**
     * Construct
     *
     * @param array|object $el
     * @param bool $convertAttributes
     */
    public function __construct(
        private array|object $el = [],
        bool $convertAttributes = true,
    ) {
        $this->original = $this->el = $this->conditionalArray($this->el);

        if ( $convertAttributes && $this->isAssoc() ) {
            foreach ($this->el as $attribute => $value) {
                $this->$attribute = $value;
            }
        }
    }

    /**
     * Obtiene un elemento de la colección
     *
     * @param string $index
     * @param mixed $default
     * @return mixed
     */
    public function get(string $index, mixed $default = null): mixed
    {
        $value = false;

        if (isset($this->el[$index])) {
            return $this->el[$index];
        }

        foreach (explode('.', $index) as $segment) {
            if ($value ? isset($value[$segment]) : isset($this->el[$segment])) {
                $value = $value ? $value[$segment] : $this->el[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Delimita si existe un elemento
     *
     * @param string $index
     * @return bool
     */
    public function has(string $index): bool
    {
        return (bool) $this->get($index, false);
    }

    /**
     * Verificar si esta NO Vacío
     *
     * @return bool
     */
    public function isNoEmpty(): bool
    {
        return (bool) count($this->el);
    }

    /**
     * Verificar si esta Vacío
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !$this->isNoEmpty();
    }

    /**
     * Convertir a object de forma condicional
     *
     * @param mixed $to
     * @return array
     */
    private function conditionalArray(mixed $to): array
    {
        return is_array($to) ? $to : (array) $to;
    }

    /**
     * Traer todos los elementos
     *
     * @return object
     */
    public function all(): object
    {
        return (object) $this->el;
    }

    /**
     * Regresa todos los valores en forma de array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->el;
    }

    /**
     * Agrega un elemento a la colección
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function add(string $key, mixed $value): static
    {
        $this->el[$key] = $value;
        return $this;
    }

    /**
     * Traer solo ciertos elementos
     *
     * @param ...$only
     * @return object
     */
    public function only(...$only): object
    {
        $get = [];

        foreach ($only as $el) {
            if (is_string($el)) {
                $get[$el] = $this->get($el);
                continue;
            }

            foreach ($el as $new) {
                $get[$new] = $this->get($new);
            }
        }

        $this->el = $this->conditionalArray($get);

        return $this->all();
    }

    /**
     * Pasa sobre la collection
     *
     * @param callable $call
     * @return Collection
     */
    public function map(callable $call): Collection
    {
        $this->el = $this->conditionalArray(
            array_map($call, $this->toArray())
        );

        return $this;
    }

    /**
     * Recorre el objeto actual
     *
     * @param callable $call
     * @return Collection
     */
    public function each(callable $call): static
    {
        foreach ($this->toArray() as $key => $value) {
            if ( is_array($value) || is_object($value) ) {
                $call(new static($value), $key);
                continue;
            }
            $call($value, $key);
        }

        return $this;
    }

    /**
     * Combinar con un nuevo arreglo
     *
     * @param array $new
     * @return $this
     */
    public function combine(mixed $new, $recursive = false): static
    {
        if ($new instanceof Collection) {
            $new = $new->toArray();
        }

        if (is_object($new)) {
            $new = (array) $new;
        }

        $this->el = $this->conditionalArray(
            $recursive
                ? array_merge_recursive($this->toArray(), $new)
                : array_merge($this->toArray(), $new)
        );

        return $this;
    }

    /**
     * Combina con el nuevo arreglo, de forma recursiva,
     * lo que implica, que se sumaran o agregaran valores
     * en lugar de ser reemplazados.
     *
     * @param mixed $new
     * @return $this
     */
    public function combineRecursive(mixed $new): static
    {
        return $this->combine($new, true);
    }

    /**
     * Actualiza un dato.
     *
     * @param string $where
     * @param mixed $update
     * @return $this
     */
    public function update(string $where, mixed $update): static
    {
        $this->setting(
            $this->updating($where, $update, $this->el)
        );

        return $this;
    }

    /**
     * Actualiza o inserta un nuevo elemento
     *
     * @param string $where
     * @param mixed $insert
     * @return $this
     */
    public function updateOrInsert(string $where, mixed $insert): static
    {
        $this->setting(
            $this->updating($where, $insert, $this->el, true)
        );

        return $this;
    }

    /**
     * @param string $where
     * @param mixed $update
     * @param array $upgradeable
     * @param bool $insert
     * @return array
     */
    private function updating(string $where, mixed $update, array &$upgradeable, bool $insert = false): array
    {
        if (isset($upgradeable[$where])) {
            $upgradeable[$where] = $update;
            return $upgradeable;
        }

        $e = explode('.', $where);

        foreach ($upgradeable as $key => $value) {
            if ($e[0] !== $key || (!isset($value[$e[1]]) && !$insert)) {
                continue;
            }

            if (is_array($value[$e[1]]) && isset($e[2])) {
                $to = $e[1];
                unset($e[0]);
                unset($e[1]);

                if (isset($value[$to])) {
                    $upgradeable[$key][$to] = $this->updating(implode('.', $e), $update,  $value[$to], $insert);
                }
                continue;
            }

            $upgradeable[$key][$e[1]] = $update;
        }

        return $upgradeable;
    }

    /**
     * Colapsa el estado actual de la collection
     *
     * @return $this
     */
    public function collapse(): static
    {
        $results = [];

        foreach ($this->el as $values) {
            if (!is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        $this->el = array_merge([], ...$results);

        return $this;
    }

    /**
     * Actualiza todos los registros con una coincidencia
     *
     * @param $searchAll
     * @param $update
     * @return Collection
     */
    public function updateAll($searchAll, $update): static
    {
        return $this->map(function ($value) use ($searchAll, $update) {
            if (isset($value[$searchAll])) {
                return array_merge($value, [$searchAll => $update]);
            }

            return $value;
        });
    }

    /**
     * Comprueba si un dato es igual a otro
     *
     * @param string $get
     * @param mixed $is
     * @param bool $strict
     * @return bool
     */
    public function is(string $get, mixed $is, bool $strict = true): bool
    {
        $get = $this->get($get);

        if ($strict) {
            return $get === $is;
        }

        return $get == $is;
    }

    /**
     * Elimina un elemento
     *
     * @param string $delete
     * @return Collection
     */
    public function delete(string $delete): Collection
    {
        // Simple
        if (isset($this->el[$delete])) {
            unset($this->el[$delete]);
        }
        // Sin, encontrar el resultado
        else {
        }

        return $this;
    }

    /**
     * Primer elemento encontrado.
     *
     * @param callable|null $call
     * @param mixed|null $default
     * @return mixed
     */
    public function first(callable $call = null, mixed $default = null): mixed
    {
        // Primer elemento
        if (is_null($call)) {
            foreach ($this->el as $el) {
                $this->setting($el);
                return $this;
            }
        }

        // Callback
        foreach ($this->el as $index => $value) {
            if ($call($value, $index)) {
                $this->setting($value);
                return $this;
            }
        }

        return $default;
    }

    /**
     * Establece el valor en el objeto
     *
     * @param $value
     * @return array
     */
    public function setting($value): array
    {
        return $this->el = $this->conditionalArray($value);
    }

    /**
     * Último elemento que concuerde con la búsqueda.
     *
     * @param callable|null $call
     * @param mixed|null $default
     * @return mixed
     */
    public function last(callable $call = null, mixed $default = null): mixed
    {
        if (is_null($call)) {
            return empty($this->el) ? $default : end($this->el);
        }

        $this->setting(
            array_reverse($this->el, true)
        );

        return $this->first($call, $default);
    }

    /**
     * Convertir a atributos HTML
     *
     * @return string
     */
    public function toHTMLAttributes(): string
    {
        $attributes = '';

        foreach ($this->el as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            $attributes .= "{$key}=\"$value\" ";
        }

        return trim($attributes, ' ');
    }

    /**
     * Define si es un arreglo asociativo
     *
     * @return bool
     */
    public function isAssoc(): bool
    {
        $keys = array_keys($this->el);

        return array_keys($keys) !== $keys;
    }

    /**
     * Búsqueda de un elemento
     *
     * @param $search
     * @param $conditionOrGet
     * @param null $get
     * @return Collection
     */
    public function where($search, $conditionOrGet, $get = null): Collection
    {
        $return = [];

        foreach ($this->original as $item) {
            if (is_null($get) && isset($item[$search]) && $item[$search] === $conditionOrGet) {
                $return[] = $item;
            }
        }

        $this->setting($return);

        return $this;
    }

    /**
     * Longitud
     *
     * @return int
     */
    public function length(): int
    {
        return count($this->el);
    }

    /**
     * Resetea el objeto con el original.
     *
     * @return $this
     */
    public function reset(): Collection
    {
        $this->el = $this->original;

        return $this;
    }
}
