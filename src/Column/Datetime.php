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

use Tobento\Service\Dater\DateFormatter;
use InvalidArgumentException;

/**
 * Datetime
 */
final class Datetime extends AbstractColumn
{
    /**
     * @var bool
     */
    protected bool $autoCreate = false;
    
    /**
     * @var bool
     */
    protected bool $autoUpdate = false;
    
    /**
     * Create a new Datetime.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(
        protected string $name,
        string $type = 'datetime',
    ) {
        if (!in_array($type, ['datetime', 'date', 'time', 'timestamp'])) {
            throw new InvalidArgumentException('Type must be one of datetime, date, time or timestamp');
        }
        
        $this->type(type: $type);
    }
    
    /**
     * Create a new instance.
     *
     * @param string $name
     * @param string $type
     * @return static
     */
    public static function new(string $name, string $type = 'datetime'): static
    {
        return new static($name, $type);
    }

    /**
     * If to automatically create the date on writing if no value passed.
     *
     * @return static $this
     */
    public function autoCreate(): static
    {
        $this->forceWriting(action: 'create');
        $this->autoCreate = true;
        return $this;
    }
    
    /**
     * If to automatically update the date on writing if no value passed..
     *
     * @return static $this
     */
    public function autoUpdate(): static
    {
        $this->forceWriting(action: 'update');
        $this->autoUpdate = true;
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
        if (is_callable($this->reader)) {
            return ($this->reader)($value, $attributes, $this->dateFormatter());
        }
        
        // only casted to string!
        return $this->getType()->cast(value: $value);
    }
    
    /**
     * Write value. Might be used for casting.
     *
     * @param mixed $value
     * @param array $attributes
     * @param string $action The action name that was performed such as 'create' or 'update'.
     * @return mixed
     */
    public function writing(mixed $value, array $attributes, string $action = ''): mixed
    {
        if ($this->autoCreate && $action === 'create' && !isset($attributes[$this->name()])) {
            return $this->formatDate('now');
        }
        
        if ($this->autoUpdate && $action === 'update' && !isset($attributes[$this->name()])) {
            return $this->formatDate('now');
        }
        
        if (is_callable($this->writer)) {
            return ($this->writer)($value, $attributes, $action, $this->dateFormatter());
        }
        
        if (empty($value) && $this->getType()->get('nullable') === true) {
            return null;
        }
        
        return $this->formatDate($value);
    }

    /**
     * Returns the formatted date.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function formatDate(mixed $value): mixed
    {
        switch ($this->getType()->type()) {
            case 'date':
                return $this->dateFormatter()->format(value: $value, format: 'Y-m-d');
            case 'time':
                return $this->dateFormatter()->format(value: $value, format: 'H:i:s');
            case 'timestamp':
                return $this->dateFormatter()->toDateTime(value: $value)->getTimestamp();
        }
        
        return $this->dateFormatter()->format(value: $value, format: 'Y-m-d H:i:s');
    }
    
    /**
     * Returns the date formatter.
     *
     * @return DateFormatter
     */
    protected function dateFormatter(): DateFormatter
    {
        return new DateFormatter();
    }
}