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
use Tobento\Service\Repository\Storage\Test\Helper\Dates;
use Tobento\Service\Dater\DateFormatter;

abstract class WriteRepositoryColumnStorableMethod extends TestCase
{
    protected null|WriteRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            new Column\Id(),
            new Column\Text('storable'),
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
    
    public function testCreateMethod()
    {
        $created = $this->repository->create([
            'storable' => '',
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
        ]);
        
        $this->assertEquals(['storable' => '', 'id' => 1], $created->toArray());
    }
    
    public function testUpdateByIdMethod()
    {
        $created = $this->repository->create(['id' => 1]);
        
        $updated = $this->repository->updateById(1, [
            'storable' => '',
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
        ]);
        
        $this->assertEquals(['storable' => '', 'id' => 1], $updated->toArray());
    }
    
    public function testUpdateMethod()
    {
        $created = $this->repository->create(['id' => 1]);
        
        $updated = $this->repository->update(where: [], attributes: [
            'storable' => '',
            'bool' => true,
            'datetime' => null,
            'float' => 1.5,
            'int' => 1,
            'json' => null,
            'text' => 'lorem',
            'trans' => ['en' => 'lorem', 'de' => 'ipsum'],
        ])->first();
        
        $this->assertEquals(['storable' => '', 'id' => 1], $updated->toArray());
    }
}