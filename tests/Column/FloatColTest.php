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
 * FloatColTest
 */
class FloatColTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $column = Column\FloatCol::new(name: 'name');
        
        $this->assertInstanceof(Column\FloatCol::class, $column);
        $this->assertInstanceof(Column\ColumnInterface::class, $column);
        $this->assertSame('name', $column->name());
        $this->assertSame('float', $column->getType()->type());
        $this->assertTrue($column->isStorable());
        $this->assertFalse($column->isTranslatable());
        $this->assertSame(1.5, $column->reading(value: 1.5, attributes: []));
        $this->assertSame(1.5, $column->writing(value: 1.5, attributes: []));
    }
    
    public function testValidTypes()
    {
        $this->assertSame('float', Column\FloatCol::new(name: 'name', type: 'float')->getType()->type());
        $this->assertSame('double', Column\FloatCol::new(name: 'name', type: 'double')->getType()->type());
        $this->assertSame('decimal', Column\FloatCol::new(name: 'name', type: 'decimal')->getType()->type());
    }
    
    public function testThrowsInvalidArgumentExceptionIfInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        
        Column\FloatCol::new(name: 'name', type: 'foo');
    }    
    
    public function testReadingMethod()
    {
        $column = Column\FloatCol::new(name: 'name');
        
        $this->assertSame(1.5, $column->reading(value: 1.5, attributes: []));
        $this->assertIsFloat($column->reading(value: true, attributes: []));
        $this->assertIsFloat($column->reading(value: 1, attributes: []));
        $this->assertIsFloat($column->reading(value: '', attributes: []));
        $this->assertIsFloat($column->reading(value: [], attributes: []));
    }
    
    public function testWritingMethod()
    {
        $column = Column\FloatCol::new(name: 'name');
        
        $this->assertSame(1.5, $column->writing(value: 1.5, attributes: []));
        $this->assertIsFloat($column->writing(value: true, attributes: []));
        $this->assertIsFloat($column->writing(value: 1, attributes: []));
        $this->assertIsFloat($column->writing(value: '', attributes: []));
        $this->assertIsFloat($column->writing(value: [], attributes: []));
    }

    public function testTypeMethod()
    {
        $column = Column\FloatCol::new(name: 'name')->type(type: 'array', param: 'value');
        
        $this->assertSame('float', $column->getType()->type());
        $this->assertSame(['type' => 'float', 'param' => 'value'], $column->getType()->parameters());
    }
    
    public function testStorableMethod()
    {
        $this->assertTrue(Column\FloatCol::new(name: 'name')->storable()->isStorable());
        $this->assertTrue(Column\FloatCol::new(name: 'name')->storable(true)->isStorable());
        $this->assertFalse(Column\FloatCol::new(name: 'name')->storable(false)->isStorable());
    }
    
    public function testReadMethod()
    {
        $reader = fn (float $value, array $attributes): float => $value * -1;
        
        $column = Column\FloatCol::new(name: 'name')->read($reader);
        
        $this->assertSame(-1.5, $column->reading(value: 1.5, attributes: []));
        $this->assertSame(0., $column->reading(value: 'foo', attributes: []));
    }
    
    public function testWriteMethod()
    {
        $writer = fn (float $value, array $attributes): float => $value * -1;
        
        $column = Column\FloatCol::new(name: 'name')->write($writer);
        
        $this->assertSame(-1.5, $column->writing(value: 1.5, attributes: []));
        $this->assertSame(0., $column->writing(value: 'foo', attributes: []));
    }
}