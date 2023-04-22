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

/**
 * AbstractColumn
 */
abstract class AbstractColumn implements ColumnInterface
{
    /**
     * @var null|Type
     */
    protected null|Type $type = null;
    
    /**
     * @var null|callable
     */
    protected $reader = null;
    
    /**
     * @var null|callable
     */
    protected $writer = null;
    
    /**
     * @var bool
     */
    protected bool $storable = true;
    
    /**
     * @var bool
     */
    protected bool $translatable = false;
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Set the attribute type.
     *
     * @param mixed ...$parameters
     * @return static $this
     */
    public function type(mixed ...$parameters): static
    {
        if ($this->type) {
            // type cannot be changed:
            $parameters['type'] = $this->type->type();
        }
        
        $this->type = new Type(...$parameters);
        return $this;
    }
    
    /**
     * Returns the type.
     *
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type ?: new Type();
    }
    
    /**
     * Set if the column is storable.
     *
     * @param bool $storable
     * @return static $this
     */
    public function storable(bool $storable = true): static
    {
        $this->storable = $storable;
        return $this;
    }
    
    /**
     * Returns whether the column is storable.
     *
     * @return bool
     */
    public function isStorable(): bool
    {
        return $this->storable;
    }
    
    /**
     * Returns true if the attribute is a translatable, otherwise false.
     *
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return $this->translatable;
    }
    
    /**
     * Set a reader.
     *
     * @param callable $reader
     * @return static $this
     */
    public function read(callable $reader): static
    {
        $this->reader = $reader;
        return $this;
    }
    
    /**
     * Read value. Might be used for casting.
     *
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function reading(mixed $value, array $attributes): mixed
    {
        // cast value from type:
        $value = $this->getType()->cast(value: $value);
        
        if (is_callable($this->reader)) {
            return ($this->reader)($value, $attributes);
        }
        
        return $value;
    }

    /**
     * Set a writer.
     *
     * @param callable $writer
     * @return static $this
     */
    public function write(callable $writer): static
    {
        $this->writer = $writer;
        return $this;
    }
    
    /**
     * Write value. Might be used for casting.
     *
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function writing(mixed $value, array $attributes): mixed
    {
        $value = $this->getType()->cast(value: $value);
        
        if (is_callable($this->writer)) {
            return ($this->writer)($value, $attributes);
        }
        
        return $value;
    }
        
    /**
     * __get For array_column object support
     */
    public function __get(string $prop)
    {
        return $this->{$prop}();
    }

    /**
     * __isset For array_column object support
     */
    public function __isset(string $prop): bool
    {
        return method_exists($this, $prop);
    }
}