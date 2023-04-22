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
 * Boolean
 */
final class Boolean extends AbstractColumn
{
    /**
     * Create a new Boolean.
     *
     * @param string $name
     */
    public function __construct(
        protected string $name,
    ) {
        $this->type(type: 'bool');
    }
    
    /**
     * Create a new instance.
     *
     * @param string $name
     * @return static
     */
    public static function new(string $name): static
    {
        return new static($name);
    }
}