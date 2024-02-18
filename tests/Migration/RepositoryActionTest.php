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

namespace Tobento\Service\Repository\Storage\Test\Migration;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Migration\RepositoryAction;
use Tobento\Service\Repository\Storage\StorageRepository;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Migration\ActionInterface;
use Tobento\Service\Migration\Action;
use Tobento\Service\Storage\InMemoryStorage;

/**
 * RepositoryActionTest
 */
class RepositoryActionTest extends TestCase
{
    protected function createRepository()
    {
        return new class(
            storage: new InMemoryStorage([]),
            table: 'products',
            columns: [
                Column\Id::new(),
                Column\Text::new('sku'),
            ],
        ) extends StorageRepository {
            //
        };
    }
    
    public function testImplementsActionInterface()
    {
        $action = new RepositoryAction(
            repository: $this->createRepository(),
        );
        
        $this->assertInstanceof(ActionInterface::class, $action);
    }
    
    public function testNewOrNullMethod()
    {
        $action = RepositoryAction::newOrNull(
            repository: $this->createRepository(),
        );
        
        $this->assertInstanceof(ActionInterface::class, $action);
    }
    
    public function testNewOrNullMethodReturnsNullActionIfUnsupportedRepo()
    {
        $action = RepositoryAction::newOrNull(
            repository: 'invalid',
        );
        
        $this->assertInstanceof(Action\NullAction::class, $action);
    }
    
    public function testNewOrFailMethod()
    {
        $action = RepositoryAction::newOrFail(
            repository: $this->createRepository(),
        );
        
        $this->assertInstanceof(ActionInterface::class, $action);
    }
    
    public function testNewOrFailMethodReturnsFailActionIfUnsupportedRepo()
    {
        $action = RepositoryAction::newOrFail(
            repository: 'invalid',
        );
        
        $this->assertInstanceof(Action\Fail::class, $action);
    }
    
    public function testIsSupportedRepositoryMethod()
    {
        $this->assertTrue(RepositoryAction::isSupportedRepository(repository: $this->createRepository()));
        
        $this->assertFalse(RepositoryAction::isSupportedRepository(repository: 'invalid'));
    }
    
    public function testNameMethod()
    {
        $action = new RepositoryAction(
            repository: $this->createRepository(),
        );
        
        $this->assertSame('products', $action->name());
    }
    
    public function testDescriptionMethod()
    {
        $action = new RepositoryAction(
            repository: $this->createRepository(),
            description: 'lorem',
        );
        
        $this->assertSame('lorem', $action->description());
    }
    
    public function testTypeMethod()
    {
        $action = new RepositoryAction(
            repository: $this->createRepository(),
            type: 'foo',
        );
        
        $this->assertSame('foo', $action->type());
    }
    
    public function testProcessMethod()
    {
        $action = new RepositoryAction(
            repository: $this->createRepository(),
            description: 'lorem',
        );
        
        $action->process();
        
        $this->assertTrue(true);
        $this->assertSame([], $action->processedDataInfo());
    }
    
    public function testProcessMethodWithItems()
    {
        $repository = $this->createRepository();
        
        $this->assertSame(0, $repository->count());
        
        $action = new RepositoryAction(
            repository: $repository,
            items: [
                ['sku' => 'foo'],
            ],
        );
        
        $action->process();
        
        $this->assertSame(1, $repository->count());
    }
}