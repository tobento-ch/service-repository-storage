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
use Tobento\Service\Repository\RepositoryInterface;
use Tobento\Service\Repository\Storage\Column;

/**
 * RepositoryWithDefaultValueTest
 */
abstract class RepositoryWithDefaultValueTest extends TestCase
{
    protected null|RepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Text::new('sku'),            
            Column\Boolean::new('bool')
                ->type(nullable: false, default: true),
            Column\Datetime::new('datetime')
                ->type(nullable: false, default: '2023-04-21 00:00:00'),
            Column\FloatCol::new('float')
                ->type(nullable: false, default: 1.5),
            Column\Integer::new('integer')
                ->type(nullable: false, default: 1),
            Column\Text::new('text')
                ->type(nullable: false, default: 'foo'),
            Column\Text::new('text_null')
                ->type(nullable: true, default: null),            
        ];
    }
    
    public function testWriting()
    {
        $created = $this->repository->create(['sku' => 'pen']);
        
        $this->assertEquals(
            [
                'sku' => 'pen',
                'bool' => true,
                'datetime' => '2023-04-21 00:00:00',
                'float' => 1.5,
                'integer' => 1,
                'text' => 'foo',
                'text_null' => '',
                'id' => 1,
            ],
            $created->all()
        );
    }
    
    public function testReading()
    {
        $this->repository->create(['sku' => 'pen']);
        
        $entity = $this->repository->findById(id: 1);
        
        $this->assertEquals(
            [
                'sku' => 'pen',
                'bool' => true,
                'datetime' => '2023-04-21 00:00:00',
                'float' => 1.5,
                'integer' => 1,
                'text' => 'foo',
                'text_null' => '',
                'id' => 1,
            ],
            $entity->all()
        );
    }
    
    public function testReadingWithNullValue()
    {
        $this->repository->create(['sku' => 'pen']);
        
        $entity = $this->repository->findOne();
        
        $this->assertSame('pen', $this->repository->findOne(where: ['text_null' => ['null']])?->get('sku'));
    }
}