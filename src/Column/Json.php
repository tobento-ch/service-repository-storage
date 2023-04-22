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
 * Json
 */
final class Json extends AbstractColumn
{
    /**
     * Create a new Json.
     *
     * @param string $name
     */
    public function __construct(
        protected string $name,
    ) {
        $this->type(type: 'json');
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