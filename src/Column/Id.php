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
 * Id
 */
final class Id extends AbstractColumn
{
    /**
     * Create a new Id.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(
        protected string $name = 'id',
        string $type = 'bigPrimary',
    ) {
        if (!in_array($type, ['primary', 'bigPrimary'])) {
            throw new InvalidArgumentException('Type must be primary or bigPrimary');
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
    public static function new(string $name = 'id', string $type = 'bigPrimary'): static
    {
        return new static($name, $type);
    }
}