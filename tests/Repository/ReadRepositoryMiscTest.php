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
use Tobento\Service\Repository\ReadRepositoryInterface;
use Tobento\Service\Repository\Storage\Test\Mock\ProductReadRepository;
use Tobento\Service\Repository\Storage\Test\Mock\ProductReadRepositoryWithColumns;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Storage\Tables\Tables;
use Tobento\Service\Storage\InMemoryStorage;

/**
 * ReadRepositoryMiscTest
 */
class ReadRepositoryMiscTest extends TestCase
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
        $repository =  new ProductReadRepository(
            storage: new InMemoryStorage(
                items: $this->getStorageItems(),
                tables: (new Tables())->add('products', ['id', 'sku', 'price'], 'id')
            ),
            table: 'products',
        );
        
        $this->assertInstanceof(ReadRepositoryInterface::class, $repository);
        $this->assertSame('pen', $repository->findById(2)?->get('sku'));
        $this->assertSame(2, $repository->findByIds(2,3,6)->count());
        $this->assertSame(3, $repository->findOne(where: ['sku' => 'pencil'])?->get('id'));
        $this->assertSame(3, $repository->findAll()->count());
        $this->assertSame(3, $repository->count());
    }
    
    public function testRepositoryWithColumns()
    {
        $repository =  new ProductReadRepository(
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
        
        $this->assertInstanceof(ReadRepositoryInterface::class, $repository);
        $this->assertSame('pen', $repository->findById(2)?->get('sku'));
        $this->assertSame(2, $repository->findByIds(2,3,6)->count());
        $this->assertSame(3, $repository->findOne(where: ['sku' => 'pencil'])?->get('id'));
        $this->assertSame(3, $repository->findAll()->count());
        $this->assertSame(3, $repository->count());
    }
    
    public function testRepositoryWithConfiguredColumns()
    {
        $repository =  new ProductReadRepositoryWithColumns(
            storage: new InMemoryStorage(
                items: $this->getStorageItems(),
            ),
            table: 'products',
        );
        
        $this->assertInstanceof(ReadRepositoryInterface::class, $repository);
        $this->assertSame('pen', $repository->findById(2)?->get('sku'));
        $this->assertSame(2, $repository->findByIds(2,3,6)->count());
        $this->assertSame(3, $repository->findOne(where: ['sku' => 'pencil'])?->get('id'));
        $this->assertSame(3, $repository->findAll()->count());
        $this->assertSame(3, $repository->count());
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
        
        $repository = new ProductReadRepository(
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