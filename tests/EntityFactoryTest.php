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

namespace Tobento\Service\Repository\Storage\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\EntityFactory;
use Tobento\Service\Repository\Storage\Column\Text;
use Tobento\Service\Repository\Storage\StorageEntityFactoryInterface;
use Tobento\Service\Repository\EntityFactoryInterface;
use Tobento\Service\Storage\Item;
use Tobento\Service\Storage\Items;

/**
 * EntityFactoryTest
 */
class EntityFactoryTest extends TestCase
{
    public function testImplementsInterfaces()
    {
        $entityFactory = new EntityFactory();
        
        $this->assertInstanceof(EntityFactoryInterface::class, $entityFactory);
        $this->assertInstanceof(StorageEntityFactoryInterface::class, $entityFactory);
    }
    
    public function testCreateEntityFromArrayMethod()
    {
        $entityFactory = new EntityFactory();
        
        $entity = $entityFactory->createEntityFromArray([]);
        
        $this->assertTrue(is_object($entity));
    }
    
    public function testCreateEntityFromStorageItemMethod()
    {
        $entityFactory = new EntityFactory();
        
        $entity = $entityFactory->createEntityFromStorageItem(new Item());
        
        $this->assertTrue(is_object($entity));
    }
    
    public function testCreateEntitiesFromStorageItemsMethod()
    {
        $entityFactory = new EntityFactory();
        
        $entities = $entityFactory->createEntitiesFromStorageItems(new Items());
        
        $this->assertTrue(is_iterable($entities));
    }
    
    public function testSetColumnsMethod()
    {
        $entityFactory = new EntityFactory();
        
        $this->assertSame($entityFactory, $entityFactory->setColumns([]));
    }
    
    public function testColumnsAreProcessed()
    {
        $entityFactory = new EntityFactory();
        
        $entityFactory->setColumns([
            Text::new(name: 'title')
                ->read(fn (string $value, array $attributes): string => ucfirst($value))
        ]);
        
        $entity = $entityFactory->createEntityFromArray(['title' => 'lorem']);
        
        $this->assertSame('Lorem', $entity->get('title'));
    }
}