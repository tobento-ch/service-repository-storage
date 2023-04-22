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
 * ColumnsInterface
 */
interface ColumnInterface
{
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string;
    
    /**
     * Returns the type.
     *
     * @return Type
     */
    public function getType(): Type;
    
    /**
     * Returns whether the column is storable.
     *
     * @return bool
     */
    public function isStorable(): bool;
    
    /**
     * Returns true if the attribute is a translatable, otherwise false.
     *
     * @return bool
     */
    public function isTranslatable(): bool;
    
    /**
     * Read value. Might be used for casting.
     *
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function reading(mixed $value, array $attributes): mixed;
    
    /**
     * Write value. Might be used for casting.
     *
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function writing(mixed $value, array $attributes): mixed;
}