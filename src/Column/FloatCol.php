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

/**
 * FloatCol
 */
final class FloatCol extends AbstractColumn
{
    /**
     * Create a new FloatCol.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(
        protected string $name,
        string $type = 'float',
    ) {
        if (!in_array($type, ['double', 'float', 'decimal'])) {
            throw new InvalidArgumentException('Type must be one of double, float or decimal');
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
    public static function new(string $name, string $type = 'float'): static
    {
        return new static($name, $type);
    }
}