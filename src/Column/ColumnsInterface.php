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

use IteratorAggregate;

/**
 * ColumnsInterface
 */
interface ColumnsInterface extends IteratorAggregate
{
    /**
     * Returns a new instance with the columns filtered.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static;
    
    /**
     * Returns a new instance with (un)storable columns only.
     *
     * @param bool $storable
     * @return static
     */
    public function storable(bool $storable = true): static;
    
    /**
     * Returns a new instance with (un)translatable columns only.
     *
     * @param bool $translatable
     * @return static
     */
    public function translatable(bool $translatable = true): static;
    
    /**
     * Returns a new instance with only with the columns specified.
     *
     * @param array $names The column names.
     * @return static
     */
    public function only(array $names): static;
    
    /**
     * Returns a new instance with all columns except the names specified.
     *
     * @param array $names The column names.
     * @return static
     */
    public function except(array $names): static;
    
    /**
     * Returns column.
     *
     * @param string $name
     * @param string $index
     * @return array
     */
    public function column(string $name, null|string $index = null): array;

    /**
     * Returns an column by name.
     *
     * @return null|ColumnInterface
     */
    public function get(string $name): null|ColumnInterface;
    
    /**
     * Returns all columns.
     *
     * @return array<string, ColumnInterface>
     */
    public function all(): array;
    
    /**
     * Returns true if columns are empty, otherwise true.
     *
     * @return bool
     */
    public function empty(): bool;
    
    /**
     * Returns the first found primary column or null if none.
     *
     * @return null|ColumnInterface
     */
    public function primary(): null|ColumnInterface;
    
    /**
     * Process reading attributes.
     *
     * @param array $attributes
     * @return array
     */
    public function processReading(array $attributes): array;
    
    /**
     * Process writing attributes.
     *
     * @param array $attributes
     * @return array
     */
    public function processWriting(array $attributes): array;
}