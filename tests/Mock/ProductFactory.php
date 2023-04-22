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

namespace Tobento\Service\Repository\Storage\Test\Mock;

use Tobento\Service\Repository\Storage\EntityFactory;

class ProductFactory extends EntityFactory
{
    public function createEntityFromArray(array $attributes): Product
    {
        // Process the columns reading:
        $attributes = $this->columns->processReading($attributes);
        
        // Create entity:
        return new Product(
            id: $attributes['id'] ?? 0,
            sku: $attributes['sku'] ?? '',
        );
    }
}