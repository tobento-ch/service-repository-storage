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
use Tobento\Service\Repository\Storage\Test\Mock\Product;

/**
 * WriteRepositoryWithCustomEntityFactoryTest
 */
abstract class WriteRepositoryWithCustomEntityFactoryTest extends TestCase
{
    protected null|WriteRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Text::new('sku'),
        ];
    }
    
    public function testCreateMethod()
    {
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $this->assertInstanceof(Product::class, $created);
    }    
    
    public function testUpdateByIdMethod()
    {
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $updated = $this->repository->updateById(id: 1, attributes: ['sku' => 'bar']);
        
        $this->assertInstanceof(Product::class, $updated);
    }
    
    public function testUpdateMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        $created = $this->repository->create(['sku' => 'scissors']);
        
        $updated = $this->repository->update(where: ['sku' => 'pen'], attributes: ['sku' => 'bar']);
        
        $this->assertInstanceof(Product::class, $updated->first());
    }
    
    public function testDeleteByIdMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        
        $deleted = $this->repository->deleteById(id: 1);
        
        $this->assertInstanceof(Product::class, $deleted);
    }
    
    public function testDeleteMethod()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        
        $deleted = $this->repository->delete(where: ['sku' => 'pen']);
        
        $this->assertInstanceof(Product::class, $deleted->first());
    }
}