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

namespace Tobento\Service\Repository\Storage;

use Tobento\Service\Repository\EntityFactoryInterface;
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Storage\ItemsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;

/**
 * StorageEntityFactoryInterface
 */
interface StorageEntityFactoryInterface extends EntityFactoryInterface
{
    /**
     * Create an entity from storage item.
     *
     * @param ItemInterface $item
     * @return object The created entity.
     */
    public function createEntityFromStorageItem(ItemInterface $item): object;
    
    /**
     * Create entities from storage items.
     *
     * @param ItemsInterface $items
     * @return iterable<object> The created entities.
     */
    public function createEntitiesFromStorageItems(ItemsInterface $items): iterable;
    
    /**
     * Sets the columns
     *
     * @param iterable<ColumnInterface>|ColumnsInterface $columns
     * @return static $this
     */
    public function setColumns(iterable|ColumnsInterface $columns): static;
}