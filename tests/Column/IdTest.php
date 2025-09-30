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

namespace Tobento\Service\Repository\Storage\Test\Column;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Column;
use InvalidArgumentException;

/**
 * IdTest
 */
class IdTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $column = new Column\Id(name: 'name');
        
        $this->assertInstanceof(Column\Id::class, $column);
        $this->assertInstanceof(Column\ColumnInterface::class, $column);
        $this->assertSame('name', $column->name());
        $this->assertSame('bigPrimary', $column->getType()->type());
        $this->assertTrue($column->isStorable());
        $this->assertFalse($column->isTranslatable());
        $this->assertSame(1, $column->reading(value: 1, attributes: []));
        $this->assertSame(1, $column->writing(value: 1, attributes: []));
    }
    
    public function testValidTypes()
    {
        $this->assertSame('bigPrimary', new Column\Id(name: 'name', type: 'bigPrimary')->getType()->type());
        $this->assertSame('primary', new Column\Id(name: 'name', type: 'primary')->getType()->type());
    }
    
    public function testThrowsInvalidArgumentExceptionIfInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new Column\Id(name: 'name', type: 'foo');
    }    
    
    public function testReadingMethod()
    {
        $column = new Column\Id(name: 'name');
        
        $this->assertSame(1, $column->reading(value: 1, attributes: []));
        $this->assertIsInt($column->reading(value: true, attributes: []));
        $this->assertIsInt($column->reading(value: '', attributes: []));
        $this->assertIsInt($column->reading(value: [], attributes: []));
    }
    
    public function testWritingMethod()
    {
        $column = new Column\Id(name: 'name');
        
        $this->assertSame(1, $column->writing(value: 1, attributes: []));
        $this->assertIsInt($column->writing(value: true, attributes: []));
        $this->assertIsInt($column->writing(value: '', attributes: []));
        $this->assertIsInt($column->writing(value: [], attributes: []));
    }

    public function testTypeMethod()
    {
        $column = new Column\Id(name: 'name')->type(type: 'array', param: 'value');
        
        $this->assertSame('bigPrimary', $column->getType()->type());
        $this->assertSame(['type' => 'bigPrimary', 'param' => 'value'], $column->getType()->parameters());
    }
    
    public function testStorableMethod()
    {
        $this->assertTrue(new Column\Id(name: 'name')->storable()->isStorable());
        $this->assertTrue(new Column\Id(name: 'name')->storable(true)->isStorable());
        $this->assertFalse(new Column\Id(name: 'name')->storable(false)->isStorable());
    }
    
    public function testReadMethod()
    {
        $reader = fn (int $value, array $attributes): int => $value * -1;
        
        $column = new Column\Id(name: 'name')->read($reader);
        
        $this->assertSame(-1, $column->reading(value: 1, attributes: []));
        $this->assertSame(-0, $column->reading(value: [], attributes: []));
    }
    
    public function testWriteMethod()
    {
        $writer = fn (int $value, array $attributes): int => $value * -1;
        
        $column = new Column\Id(name: 'name')->write($writer);
        
        $this->assertSame(-1, $column->writing(value: 1, attributes: []));
        $this->assertSame(-0, $column->writing(value: [], attributes: []));
    }
}