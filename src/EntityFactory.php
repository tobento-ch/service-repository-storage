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
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;
use Tobento\Service\Repository\Storage\Column\Columns;
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Storage\ItemsInterface;
use Tobento\Service\Storage\Item;
use Tobento\Service\Iterable\Iter;

/**
 * EntityFactory
 */
class EntityFactory implements EntityFactoryInterface, StorageEntityFactoryInterface
{
    /**
     * @var ColumnsInterface
     */
    protected ColumnsInterface $columns;
    
    /**
     * Create a new EntityFactory.
     *
     * @param null|iterable<ColumnInterface>|ColumnsInterface $columns
     */
    public function __construct(
        null|iterable|ColumnsInterface $columns = null,
    ) {
        if (is_null($columns)) {
            $columns = new Columns();
        }
        
        $this->setColumns($columns);
    }
    
    /**
     * Create an entity from array.
     *
     * @param array $attributes
     * @return object The created entity.
     */
    public function createEntityFromArray(array $attributes): object
    {
        $attributes = $this->columns->processReading($attributes);

        return new Item($attributes);
    }
    
    /**
     * Create an entity from storage item.
     *
     * @param ItemInterface $item
     * @return object The created entity.
     */
    public function createEntityFromStorageItem(ItemInterface $item): object
    {
        return $this->createEntityFromArray($item->all());
    }
    
    /**
     * Create entities from storage items.
     *
     * @param ItemsInterface $items
     * @return iterable<object> The created entities.
     */
    public function createEntitiesFromStorageItems(ItemsInterface $items): iterable
    {
        return $items->map(function(array $item): object {
            return $this->createEntityFromArray($item);
        });
    }
        
    /**
     * Sets the columns
     *
     * @param iterable<ColumnInterface>|ColumnsInterface $columns
     * @return static $this
     */
    public function setColumns(iterable|ColumnsInterface $columns): static
    {
        if (! $columns instanceof ColumnsInterface) {
            $columns = Iter::toArray($columns);
            $columns = new Columns(...$columns);
        }
        
        $this->columns = $columns;
        return $this;
    }
}