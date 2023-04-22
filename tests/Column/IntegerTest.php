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
 * IntegerTest
 */
class IntegerTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $column = Column\Integer::new(name: 'name');
        
        $this->assertInstanceof(Column\Integer::class, $column);
        $this->assertInstanceof(Column\ColumnInterface::class, $column);
        $this->assertSame('name', $column->name());
        $this->assertSame('int', $column->getType()->type());
        $this->assertTrue($column->isStorable());
        $this->assertFalse($column->isTranslatable());
        $this->assertSame(1, $column->reading(value: 1, attributes: []));
        $this->assertSame(1, $column->writing(value: 1, attributes: []));
    }
    
    public function testValidTypes()
    {
        $this->assertSame('int', Column\Integer::new(name: 'name', type: 'int')->getType()->type());
        $this->assertSame('tinyInt', Column\Integer::new(name: 'name', type: 'tinyInt')->getType()->type());
        $this->assertSame('bigInt', Column\Integer::new(name: 'name', type: 'bigInt')->getType()->type());
    }
    
    public function testThrowsInvalidArgumentExceptionIfInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        
        Column\Integer::new(name: 'name', type: 'foo');
    }    
    
    public function testReadingMethod()
    {
        $column = Column\Integer::new(name: 'name');
        
        $this->assertSame(1, $column->reading(value: 1, attributes: []));
        $this->assertIsInt($column->reading(value: true, attributes: []));
        $this->assertIsInt($column->reading(value: '', attributes: []));
        $this->assertIsInt($column->reading(value: [], attributes: []));
    }
    
    public function testWritingMethod()
    {
        $column = Column\Integer::new(name: 'name');
        
        $this->assertSame(1, $column->writing(value: 1, attributes: []));
        $this->assertIsInt($column->writing(value: true, attributes: []));
        $this->assertIsInt($column->writing(value: '', attributes: []));
        $this->assertIsInt($column->writing(value: [], attributes: []));
    }

    public function testTypeMethod()
    {
        $column = Column\Integer::new(name: 'name')->type(type: 'array', param: 'value');
        
        $this->assertSame('int', $column->getType()->type());
        $this->assertSame(['type' => 'int', 'param' => 'value'], $column->getType()->parameters());
    }
    
    public function testStorableMethod()
    {
        $this->assertTrue(Column\Integer::new(name: 'name')->storable()->isStorable());
        $this->assertTrue(Column\Integer::new(name: 'name')->storable(true)->isStorable());
        $this->assertFalse(Column\Integer::new(name: 'name')->storable(false)->isStorable());
    }
    
    public function testReadMethod()
    {
        $reader = fn (int $value, array $attributes): int => $value * -1;
        
        $column = Column\Integer::new(name: 'name')->read($reader);
        
        $this->assertSame(-1, $column->reading(value: 1, attributes: []));
        $this->assertSame(-0, $column->reading(value: [], attributes: []));
    }
    
    public function testWriteMethod()
    {
        $writer = fn (int $value, array $attributes): int => $value * -1;
        
        $column = Column\Integer::new(name: 'name')->write($writer);
        
        $this->assertSame(-1, $column->writing(value: 1, attributes: []));
        $this->assertSame(-0, $column->writing(value: [], attributes: []));
    }
}