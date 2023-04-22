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

namespace Tobento\Service\Repository\Storage\Test\Repository;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\WriteRepositoryInterface;
use Tobento\Service\Repository\Storage\Test\Mock\ProductWriteRepository;
use Tobento\Service\Repository\Storage\Test\Mock\ProductWriteRepositoryWithColumns;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Storage\Tables\Tables;
use Tobento\Service\Storage\InMemoryStorage;

/**
 * WriteRepositoryMiscTest
 */
class WriteRepositoryMiscTest extends TestCase
{
    public function getStorageItems(): array
    {
        return [
            'products' => [
                1 => ['id' => 1, 'sku' => 'paper', 'price' => 1.2],
                2 => ['id' => 2, 'sku' => 'pen', 'price' => 1.8],
                3 => ['id' => 3, 'sku' => 'pencil', 'price' => 1.5],
            ],
        ];
    }
    
    public function testRepositoryWithStorageTables()
    {
        $repository =  new ProductWriteRepository(
            storage: new InMemoryStorage(
                items: $this->getStorageItems(),
                tables: (new Tables())->add('products', ['id', 'sku', 'price'], 'id')
            ),
            table: 'products',
        );
        
        $this->assertInstanceof(WriteRepositoryInterface::class, $repository);
        $this->assertSame(4, $repository->create(['sku' => 'scissors'])?->get('id'));
        $this->assertSame(2.5, $repository->updateById(id: 2, attributes: ['price' => 2.5])?->get('price'));
        $this->assertSame(4, $repository->update(where: [], attributes: ['price' => 2.5])->count());
        $this->assertSame(2, $repository->deleteById(id: 2)?->get('id'));
        $this->assertSame(3, $repository->delete(where: [])->count());
    }
    
    public function testRepositoryWithColumns()
    {
        $repository =  new ProductWriteRepository(
            storage: new InMemoryStorage(
                items: $this->getStorageItems(),
            ),
            table: 'products',
            columns: [
                Column\Id::new(),
                Column\Text::new('sku'),
                Column\FloatCol::new('price'),
            ],
        );
        
        $this->assertInstanceof(WriteRepositoryInterface::class, $repository);
        $this->assertSame(4, $repository->create(['sku' => 'scissors'])?->get('id'));
        $this->assertSame(2.5, $repository->updateById(id: 2, attributes: ['price' => 2.5])?->get('price'));
        $this->assertSame(4, $repository->update(where: [], attributes: ['price' => 2.5])->count());
        $this->assertSame(2, $repository->deleteById(id: 2)?->get('id'));
        $this->assertSame(3, $repository->delete(where: [])->count());
    }
    
    public function testRepositoryWithConfiguredColumns()
    {
        $repository =  new ProductWriteRepositoryWithColumns(
            storage: new InMemoryStorage(
                items: $this->getStorageItems(),
            ),
            table: 'products',
        );
        
        $this->assertInstanceof(WriteRepositoryInterface::class, $repository);
        $this->assertSame(4, $repository->create(['sku' => 'scissors'])?->get('id'));
        $this->assertSame(2.5, $repository->updateById(id: 2, attributes: ['price' => 2.5])?->get('price'));
        $this->assertSame(4, $repository->update(where: [], attributes: ['price' => 2.5])->count());
        $this->assertSame(2, $repository->deleteById(id: 2)?->get('id'));
        $this->assertSame(3, $repository->delete(where: [])->count());
    }
    
    public function testStorageMethods()
    {
        $storage = new InMemoryStorage(
            items: $this->getStorageItems(),
        );
        
        $columns = new Column\Columns(
            Column\Id::new(),
            Column\Text::new('sku'),
            Column\FloatCol::new('price'),
        );
        
        $repository = new ProductWriteRepository(
            storage: $storage,
            table: 'products',
            columns: $columns,            
        );
        
        $this->assertSame($storage, $repository->storage());
        $this->assertSame($storage, $repository->query());
        $this->assertSame('products', $repository->table());
        $this->assertSame($columns, $repository->columns());
    }
}