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

/**
 * WriteRepositoryColumnStorableMethodTest
 */
abstract class WriteRepositoryColumnStorableMethodTest extends TestCase
{
    protected null|WriteRepositoryInterface $repository = null;

    public function getColumns(): array
    {
        return [
            Column\Id::new(),
            Column\Text::new('storable'),
            Column\Boolean::new('bool')
                ->storable(false),
            Column\Datetime::new('datetime')
                ->storable(false),
            Column\FloatCol::new('float')
                ->storable(false),
            Column\Integer::new('int')
                ->storable(false),
            Column\Json::new('json')
                ->storable(false),
            Column\Text::new('text')
                ->storable(false),
            Column\Translatable::new('trans')
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