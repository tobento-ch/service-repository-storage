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
 * Integer
 */
final class Integer extends AbstractColumn
{
    /**
     * Create a new Integer.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(
        protected string $name,
        string $type = 'int',
    ) {
        if (!in_array($type, ['int', 'tinyInt', 'bigInt'])) {
            throw new InvalidArgumentException('Type must be one of int, tinyInt or bigInt');
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
    public static function new(string $name, string $type = 'int'): static
    {
        return new static($name, $type);
    }
}