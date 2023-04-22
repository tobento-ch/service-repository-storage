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
     * @return mixed
     */
    public function writing(mixed $value, array $attributes): mixed
    {
        if (is_callable($this->writer)) {
            return ($this->writer)($value, $attributes, $this->dateFormatter());
        }
        
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