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
 * Text
 */
final class Text extends AbstractColumn
{
    /**
     * Create a new Text.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(
        protected string $name,
        string $type = 'string',
    ) {
        if (!in_array($type, ['char', 'string', 'text'])) {
            throw new InvalidArgumentException('Type must be one of char, string or text');
        }
        
        $this->type(type: $type);
    }
}