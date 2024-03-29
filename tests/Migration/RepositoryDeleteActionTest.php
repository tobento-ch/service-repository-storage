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
use Tobento\Service\Repository\Storage\Migration\RepositoryDeleteAction;
use Tobento\Service\Repository\Storage\StorageRepository;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Migration\ActionInterface;
use Tobento\Service\Migration\Action;
use Tobento\Service\Storage\InMemoryStorage;

/**
 * RepositoryDeleteActionTest
 */
class RepositoryDeleteActionTest extends TestCase
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
        $action = new RepositoryDeleteAction(
            repository: $this->createRepository(),
        );
        
        $this->assertInstanceof(ActionInterface::class, $action);
    }
    
    public function testNewOrNullMethod()
    {
        $action = RepositoryDeleteAction::newOrNull(
            repository: $this->createRepository(),
        );
        
        $this->assertInstanceof(ActionInterface::class, $action);
    }
    
    public function testNewOrNullMethodReturnsNullActionIfUnsupportedRepo()
    {
        $action = RepositoryDeleteAction::newOrNull(
            repository: 'invalid',
        );
        
        $this->assertInstanceof(Action\NullAction::class, $action);
    }
    
    public function testNewOrFailMethod()
    {
        $action = RepositoryDeleteAction::newOrFail(
            repository: $this->createRepository(),
        );
        
        $this->assertInstanceof(ActionInterface::class, $action);
    }
    
    public function testNewOrFailMethodReturnsFailActionIfUnsupportedRepo()
    {
        $action = RepositoryDeleteAction::newOrFail(
            repository: 'invalid',
        );
        
        $this->assertInstanceof(Action\Fail::class, $action);
    }
    
    public function testIsSupportedRepositoryMethod()
    {
        $this->assertTrue(RepositoryDeleteAction::isSupportedRepository(repository: $this->createRepository()));
        
        $this->assertFalse(RepositoryDeleteAction::isSupportedRepository(repository: 'invalid'));
    }
    
    public function testNameMethod()
    {
        $action = new RepositoryDeleteAction(
            repository: $this->createRepository(),
        );
        
        $this->assertSame('products', $action->name());
    }
    
    public function testDescriptionMethod()
    {
        $action = new RepositoryDeleteAction(
            repository: $this->createRepository(),
            description: 'lorem',
        );
        
        $this->assertSame('lorem', $action->description());
    }
    
    public function testTypeMethod()
    {
        $action = new RepositoryDeleteAction(
            repository: $this->createRepository(),
            type: 'foo',
        );
        
        $this->assertSame('foo', $action->type());
    }
    
    public function testProcessMethod()
    {
        $action = new RepositoryDeleteAction(
            repository: $this->createRepository(),
            description: 'lorem',
        );
        
        $action->process();
        
        $this->assertTrue(true);
        $this->assertSame([], $action->processedDataInfo());
    }
}