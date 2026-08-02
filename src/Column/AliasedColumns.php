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

class AliasedColumns extends Columns
{
    protected array $aliases = [];
    
    protected bool $readonlyAliases = true;
    
    /**
     * Returns a new instance with the given aliases.
     *
     * @param array<string, string> $aliases
     * @param bool $readonly
     * @return static
     */
    public function withAliases(array $aliases, bool $readonly = true): static
    {
        $filtered = [];

        foreach ($aliases as $alias => $raw) {

            // Skip if alias name collides with a real column.
            if (!is_null(parent::get($alias))) {
                continue;
            }

            // Allow JSON path: column->index
            if (str_contains($raw, '->')) {
                [$name] = explode('->', $raw, 2);

                // raw column must exist
                if (is_null(parent::get($name))) {
                    continue;
                }

                // JSON path is valid → accept alias
                $filtered[$alias] = $raw;
                continue;
            }

            // Normal column must exist
            if (is_null(parent::get($raw))) {
                continue;
            }

            $filtered[$alias] = $raw;
        }

        $new = clone $this;
        $new->aliases = $filtered;
        $new->readonlyAliases = $readonly;
        return $new;
    }
    
    /**
     * Returns the aliases.
     *
     * @return array<string, string>
     */
    public function aliases(): array
    {
        return $this->aliases;
    }
    
    /**
     * Returns whether aliases are readonly.
     *
     * @return bool
     */
    public function readonlyAliases(): bool
    {
        return $this->readonlyAliases;
    }
    
    /**
     * Returns a column by name.
     *
     * @return null|ColumnInterface
     */
    public function get(string $name): null|ColumnInterface
    {
        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }
        
        return parent::get($name);
    }
    
    /**
     * Process reading attributes.
     *
     * @param array $attributes
     * @return array
     */
    public function processReading(array $attributes): array
    {
        $attributes = parent::processReading($attributes);

        foreach ($this->aliases as $alias => $raw) {

            // JSON path alias: column->index
            if (str_contains($raw, '->')) {
                [$name, $index] = explode('->', $raw, 2);

                if (isset($attributes[$name]) 
                    && is_array($attributes[$name]) 
                    && array_key_exists($index, $attributes[$name])
                ) {
                    $attributes[$alias] = $attributes[$name][$index];
                }

                continue;
            }

            // Normal alias
            if (array_key_exists($raw, $attributes)) {
                $attributes[$alias] = $attributes[$raw];
            }
        }

        return $attributes;
    }
    
    /**
     * Process writing attributes.
     *
     * @param array $attributes
     * @param string $action The action name that was performed such as 'create' or 'update'.
     * @return array
     */
    public function processWriting(array $attributes, string $action): array
    {
        foreach ($this->aliases as $alias => $raw) {

            if (!array_key_exists($alias, $attributes)) {
                continue;
            }

            if ($this->readonlyAliases) {
                unset($attributes[$alias]);
                continue;
            }

            // JSON path alias: column->index
            if (str_contains($raw, '->')) {
                [$name, $index] = explode('->', $raw, 2);

                // Ensure base column exists
                if (!isset($attributes[$name]) || !is_array($attributes[$name])) {
                    $attributes[$name] = [];
                }

                // Write into JSON structure
                $attributes[$name][$index] = $attributes[$alias];

                unset($attributes[$alias]);
                continue;
            }

            // Normal alias
            $attributes[$raw] = $attributes[$alias];
            unset($attributes[$alias]);
        }

        return parent::processWriting($attributes, $action);
    }
    
    /**
     * Allows modifying the where conditions before applying them.
     *
     * @param array $where The original where conditions.
     * @return array The modified where conditions.
     */
    public function mayModifyWhere(array $where): array
    {
        $resolved = [];

        foreach ($where as $column => $value) {

            // Nested AND/OR groups
            if (!is_string($column)) {
                $resolved[$column] = is_array($value)
                    ? $this->mayModifyWhere($value)
                    : $value;
                continue;
            }

            // Alias → raw
            $raw = $this->aliases[$column] ?? $column;

            $resolved[$raw] = $value;
        }
        
        return $resolved;
    }

    /**
     * Allows modifying the order-by definitions before applying them.
     *
     * @param array $orderBy The original order-by definitions.
     * @return array The modified order-by definitions.
     */
    public function mayModifyOrderBy(array $orderBy): array
    {
        $resolved = [];

        foreach ($orderBy as $column => $direction) {
            $raw = $this->aliases[$column] ?? $column;
            $resolved[$raw] = $direction;
        }

        return $resolved;
    }
    
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
    public function mayModifyColumn(string $column): string
    {
        return $this->aliases[$column] ?? $column;
    }
}