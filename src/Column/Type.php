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

use InvalidArgumentException;
use JsonException;

/**
 * Type
 */
class Type
{
    /**
     * @var array
     */
    protected array $parameters = [];
    
    /**
     * @var array
     */
    protected array $typesToCasts = [
        'primary' => 'int',
        'bigPrimary' => 'int',
        'bool' => 'bool',
        'int' => 'int',
        'tinyInt' => 'int',
        'bigInt' => 'int',
        'char' => 'string',
        'string' => 'string',
        'text' => 'string',
        'double' => 'float',
        'float' => 'float',
        'decimal' => 'float',
        'datetime' => 'datetime',
        'date' => 'date',
        'time' => 'time',
        'timestamp' => 'timestamp',
        'json' => 'array',
        'array' => 'array',
    ];
    
    /**
     * Create a new Type.
     *
     * @param mixed ...$parameters
     */
    public function __construct(mixed ...$parameters)
    {
        $this->parameters = $parameters;
    }
    
    /**
     * Returns true if the type is primary, otherwise false.
     *
     * @return bool
     */
    public function isPrimary(): bool
    {
        if ($this->type() === 'primary' || $this->type() === 'bigPrimary') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the type.
     *
     * @return string
     */
    public function type(): string
    {
        $type = $this->get('type', 'string');
        
        return is_string($type) ? $type : 'string';
    }
    
    /**
     * Returns the parameters.
     *
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns true if the parameter exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }
    
    /**
     * Returns the named parameter if exists, otherwise returns the default value.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->parameters[$name] ?? $default;
    }
    
    /**
     * Casts the specified value to the type.
     *
     * @param mixed $value
     * @param null|string $type
     * @return mixed $default
     */
    public function cast(mixed $value, null|string $type = null, mixed $default = null): mixed
    {
        $type = $type ?: $this->type();
        $default = $default ?: $this->get('default');
        $castType = $this->typesToCasts[$type] ?? null;
        
        if (is_null($castType)) {
            return $value;
        }
        
        switch ($castType) {
            case 'int':
                return is_numeric($value) ? (int) $value : (int) $default;
            case 'float':
                return is_numeric($value) ? (float) $value : (float) $default;
            case 'string':
                return is_scalar($value) ? (string) $value : (string) $default;
            case 'bool':
                return is_scalar($value) ? (bool) $value : (bool) $default;
            case 'array':
                return $this->toArray($value, $default);
            case 'datetime':
                // only ensure string
                return is_scalar($value) ? (string) $value : (string) $default;
            case 'date':
                // only ensure string
                return is_scalar($value) ? (string) $value : (string) $default;
            case 'time':
                // only ensure string
                return is_scalar($value) ? (string) $value : (string) $default;
            case 'timestamp':
                // only ensure string
                return is_scalar($value) ? (string) $value : (string) $default;
        }
        
        throw new InvalidArgumentException(sprintf('Unsupported cast type %s', $castType));
    }
    
    /**
     * Cast to array.
     *
     * @param mixed $value
     * @param mixed $default
     * @return array
     */
    protected function toArray(mixed $value, mixed $default = []): array
    {
        switch (gettype($value))
        {
            case 'array':
                return $value;
            case 'string':
                try {
                    $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                    return is_array($decoded) ? $decoded : $this->toArray($default);
                } catch (JsonException $e) {
                    return $this->toArray($default);
                }
        }
        
        return $this->toArray($default);
    }
}