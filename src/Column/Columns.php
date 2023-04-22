<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Repository\Storage\Column;

use Tobento\Service\Collection\Arr;
use Traversable;
use ArrayIterator;

/**
 * Columns
 */
class Columns implements ColumnsInterface
{
    /**
     * @var array<string, ColumnInterface>
     */
    protected array $columns = [];
    
    /**
     * Create a new Columns.
     *
     * @param ColumnInterface $columns
     */
    public function __construct(
        ColumnInterface ...$columns,
    ) {
        foreach($columns as $column) {
            $this->columns[$column->name()] = $column;
        }
    }
    
    /**
     * Returns a new instance with the columns filtered.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $new = clone $this;
        $new->columns = array_filter($this->columns, $callback);
        return $new;
    }
    
    /**
     * Returns a new instance with (un)storable columns only.
     *
     * @param bool $storable
     * @return static
     */
    public function storable(bool $storable = true): static
    {
        return $this->filter(
            fn(ColumnInterface $c): bool => $c->isStorable() === $storable
        );
    }
    
    /**
     * Returns a new instance with (un)translatable columns only.
     *
     * @param bool $translatable
     * @return static
     */
    public function translatable(bool $translatable = true): static
    {
        return $this->filter(
            fn(ColumnInterface $c): bool => $c->isTranslatable() === $translatable
        );
    }

    /**
     * Returns a new instance with only with the columns specified.
     *
     * @param array $names The column names.
     * @return static
     */
    public function only(array $names): static
    {
        $new = clone $this;
        $new->columns = Arr::only($this->all(), $names);
        return $new;
    }
    
    /**
     * Returns a new instance with all columns except the names specified.
     *
     * @param array $names The column names.
     * @return static
     */
    public function except(array $names): static
    {
        $new = clone $this;
        $new->columns = Arr::except($this->all(), $names);
        return $new;
    }
    
    /**
     * Returns column.
     *
     * @param string $name
     * @param string $index
     * @return array
     */
    public function column(string $name, null|string $index = null): array
    {
        return array_column($this->all(), $name, $index);
    }

    /**
     * Returns a column by name.
     *
     * @return null|ColumnInterface
     */
    public function get(string $name): null|ColumnInterface
    {
        return $this->columns[$name] ?? null;
    }
    
    /**
     * Returns all columns.
     *
     * @return array<string, ColumnInterface>
     */
    public function all(): array
    {
        return $this->columns;
    }
    
    /**
     * Returns true if columns are empty, otherwise true.
     *
     * @return bool
     */
    public function empty(): bool
    {
        return empty($this->columns);
    }
    
    /**
     * Returns the first found primary column or null if none.
     *
     * @return null|ColumnInterface
     */
    public function primary(): null|ColumnInterface
    {
        foreach($this->all() as $column) {
            if ($column->getType()->isPrimary()) {
                return $column;
            }
        }

        return null;
    }
    
    /**
     * Process reading attributes.
     *
     * @param array $attributes
     * @return array
     */
    public function processReading(array $attributes): array
    {
        foreach($attributes as $name => $value) {            
            if (is_null($column = $this->get(name: $name))) {
                continue;
            }
            
            $attributes[$name] = $column->reading(value: $value, attributes: $attributes);
        }
        
        return $attributes;
    }
    
    /**
     * Process writing attributes.
     *
     * @param array $attributes
     * @return array
     */
    public function processWriting(array $attributes): array
    {
        foreach($attributes as $name => $value) {            
            if (is_null($column = $this->get(name: $name))) {
                continue;
            }
            
            $attributes[$name] = $column->writing(value: $value, attributes: $attributes);
        }

        // handle default parameter type:
        $columns = $this->except(array_keys($attributes));
        
        foreach($columns as $column) {
            if (! $column->getType()->has('default')) {
                continue;
            }
            
            $attributes[$column->name()] = $column->getType()->get('default');
        }
        
        return $attributes;
    }
    
    /**
     * Get iterator.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }
}