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
use Tobento\Service\Repository\ReadRepositoryInterface;
use Tobento\Service\Repository\WriteRepositoryInterface;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Repository\Storage\Test\Mock\Product;

/**
 * ReadRepositoryWithCustomEntityFactoryTest
 */
abstract class ReadRepositoryWithCustomEntityFactoryTest extends TestCase
{
    protected null|ReadRepositoryInterface $repository = null;
    
    protected null|WriteRepositoryInterface $writeRepository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Text::new('sku'),
        ];
    }
    
    public function testFindByIdMethod()
    {
        $this->writeRepository->create(['sku' => 'scissors']);
        
        $entity = $this->repository->findById(id: 1);
        
        $this->assertInstanceof(Product::class, $entity);
    }
    
    public function testFindByIdsMethod()
    {
        $this->writeRepository->create(['sku' => 'scissors']);
        
        $entities = $this->repository->findByIds(1);
        
        $this->assertInstanceof(Product::class, $entities[1] ?? null);
    }
    
    public function testFindOneMethod()
    {
        $this->writeRepository->create(['sku' => 'scissors']);
        
        $entity = $this->repository->findOne();
        
        $this->assertInstanceof(Product::class, $entity);
    }

    public function AtestFindAllMethod()
    {
        $this->writeRepository->create(['sku' => 'scissors']);
        
        $entities = $this->repository->findAll();
        
        $this->assertInstanceof(Product::class, $entities[1] ?? null);
    }
}