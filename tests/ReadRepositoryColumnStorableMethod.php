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
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Repository\Storage\Test\Helper\Dates;
use Tobento\Service\Dater\DateFormatter;

abstract class ReadRepositoryColumnStorableMethod extends TestCase
{
    protected null|ReadRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            new Column\Id(),
            new Column\Boolean('bool')
                ->storable(false),
            new Column\Datetime('datetime')
                ->storable(false),
            new Column\FloatCol('float')
                ->storable(false),
            new Column\Integer('int')
                ->storable(false),
            new Column\Json('json')
                ->storable(false),
            new Column\Text('text')
                ->storable(false),
            new Column\Translatable('trans')
                ->storable(false),
        ];
    }
    
    public function testFindByIdMethod()
    {
        $item = $this->repository->storage()->table($this->repository->table())->insert([
            'id' => 1,
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
        ]);
        
        $entity = $this->repository->findById(id: 1);
        
        $this->assertSame(['id' => 1], $entity->toArray());
    }
    
    public function testFindByIdsMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'id' => 1,
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
        ]);
        
        $entity = $this->repository->findByIds(1)->first();
        
        $this->assertSame(['id' => 1], $entity->toArray());
    }
    
    public function testFindOneMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'id' => 1,
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
        ]);
        
        $entity = $this->repository->findOne();
        
        $this->assertSame(['id' => 1], $entity->toArray());
    }
    
    public function testFindAllMethod()
    {
        $this->repository->storage()->table($this->repository->table())->insert([
            'id' => 1,
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
        ]);
        
        $entity = $this->repository->findAll()->first();
        
        $this->assertSame(['id' => 1], $entity->toArray());
    }    
}