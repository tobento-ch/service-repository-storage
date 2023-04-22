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
use Tobento\Service\Repository\WriteRepositoryInterface;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Repository\RepositoryUpdateException;
use Tobento\Service\Repository\RepositoryDeleteException;
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Storage\ItemsInterface;

/**
 * WriteRepositoryTest
 */
abstract class WriteRepositoryTest extends TestCase
{
    protected null|WriteRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Boolean::new('active'),
            Column\Datetime::new('created'),
            Column\FloatCol::new('price'),
            Column\Integer::new('count'),
            Column\Json::new('options'),
            Column\Text::new('sku'),
            Column\Translatable::new('title'),
        ];
    }
    
    public function testCreateMethod()
    {
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $this->assertInstanceof(ItemInterface::class, $created);
        $this->assertSame(1, $created->get('id'));
    }
    
    public function testUpdateByIdMethod()
    {
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $updated = $this->repository->updateById(id: 1, attributes: ['price' => 2.5]);
        
        $this->assertInstanceof(ItemInterface::class, $updated);
        $this->assertSame(1, $updated->get('id'));
        $this->assertSame(2.5, $updated->get('price'));
    }
    
    public function testUpdateByIdMethodThrowsRepositoryUpdateExceptionIfNotExists()
    {
        $this->expectException(RepositoryUpdateException::class);
        
        $updated = $this->repository->updateById(id: 1, attributes: ['price' => 2.5]);
    }
    
    public function testUpdateMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $updated = $this->repository->update(where: ['sku' => 'pen'], attributes: ['price' => 2.5]);
        
        $this->assertInstanceof(ItemsInterface::class, $updated);
        $this->assertSame(1, $updated->count());
        $this->assertSame('pen', $updated->first()?->get('sku'));
    }
    
    public function testUpdateMethodWithInvalidWhereParametersShouldBeIgnored()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $updated = $this->repository->update(where: ['sku' => []], attributes: ['price' => 2.5]);
        $this->assertSame(0, $updated->count());
                
        $updated = $this->repository->update(where: ['sku' => [[]]], attributes: ['price' => 2.5]);
        $this->assertSame(0, $updated->count());
    }
    
    public function testDeleteByIdMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $deleted = $this->repository->deleteById(id: 2);
        
        $this->assertInstanceof(ItemInterface::class, $deleted);
        $this->assertSame(2, $deleted->get('id'));
        $this->assertSame('scissors', $deleted->get('sku'));
    }
    
    public function testDeleteByIdMethodThrowsRepositoryDeleteExceptionIfNotExists()
    {
        $this->expectException(RepositoryDeleteException::class);
        
        $deleted = $this->repository->deleteById(id: 2);
    }
    
    public function testDeleteMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $deleted = $this->repository->delete(where: ['sku' => 'scissors']);
        
        $this->assertInstanceof(ItemsInterface::class, $deleted);
        $this->assertSame(1, $deleted->count());
        $this->assertSame('scissors', $deleted->first()?->get('sku'));
    }
    
    public function testDeleteMethodWithInvalidWhereParametersShouldBeIgnored()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $deleted = $this->repository->delete(where: ['sku' => []]);
        $this->assertSame(0, $deleted->count());
        
        $deleted = $this->repository->delete(where: ['sku' => [[]]]);
        $this->assertSame(0, $deleted->count());
    }
}