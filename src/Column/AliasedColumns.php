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
            // Skip if the raw target column doesn't exist.
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

            $attributes[$raw] = $attributes[$alias];
            unset($attributes[$alias]);
        }

        return parent::processWriting($attributes, $action);
    }
}