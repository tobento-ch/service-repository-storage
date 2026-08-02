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
 * @extends IteratorAggregate<string, ColumnInterface>
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
     * @param string $action The action name that was performed such as 'create' or 'update'.
     * @return array
     */
    public function processWriting(array $attributes, string $action): array;
    
    /**
     * Allows modifying the where conditions before applying them.
     *
     * @param array $where The original where conditions.
     * @return array The modified where conditions.
     */
    public function mayModifyWhere(array $where): array;

    /**
     * Allows modifying the order-by definitions before applying them.
     *
     * @param array $orderBy The original order-by definitions.
     * @return array The modified order-by definitions.
     */
    public function mayModifyOrderBy(array $orderBy): array;
    
    /**
     * Allows modifying a single column name before it is used
     * for column extraction (e.g. in findColumn).
     *
     * This is used to resolve alias names to their underlying
     * raw column names, including JSON path columns such as
     * "options->color".
     *
     * @param string $column The original column name.
     * @return string The modified (raw) column name.
     */
    public function mayModifyColumn(string $column): string;
}