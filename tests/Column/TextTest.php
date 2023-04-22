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
 * TextTest
 */
class TextTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $column = Column\Text::new(name: 'name');
        
        $this->assertInstanceof(Column\Text::class, $column);
        $this->assertInstanceof(Column\ColumnInterface::class, $column);
        $this->assertSame('name', $column->name());
        $this->assertSame('string', $column->getType()->type());
        $this->assertTrue($column->isStorable());
        $this->assertFalse($column->isTranslatable());
        $this->assertSame('lorem', $column->reading(value: 'lorem', attributes: []));
        $this->assertSame('lorem', $column->writing(value: 'lorem', attributes: []));
    }
    
    public function testValidTypes()
    {
        $this->assertSame('string', Column\Text::new(name: 'name', type: 'string')->getType()->type());
        $this->assertSame('char', Column\Text::new(name: 'name', type: 'char')->getType()->type());
        $this->assertSame('text', Column\Text::new(name: 'name', type: 'text')->getType()->type());
    }
    
    public function testThrowsInvalidArgumentExceptionIfInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        
        Column\Text::new(name: 'name', type: 'foo');
    }    
    
    public function testReadingMethod()
    {
        $column = Column\Text::new(name: 'name');
        
        $this->assertSame('lorem', $column->reading(value: 'lorem', attributes: []));
        $this->assertIsString($column->reading(value: true, attributes: []));
        $this->assertIsString($column->reading(value: '', attributes: []));
        $this->assertIsString($column->reading(value: [], attributes: []));
    }
    
    public function testWritingMethod()
    {
        $column = Column\Text::new(name: 'name');
        
        $this->assertSame('lorem', $column->writing(value: 'lorem', attributes: []));
        $this->assertIsString($column->writing(value: true, attributes: []));
        $this->assertIsString($column->writing(value: '', attributes: []));
        $this->assertIsString($column->writing(value: [], attributes: []));
    }

    public function testTypeMethod()
    {
        $column = Column\Text::new(name: 'name')->type(type: 'array', param: 'value');
        
        $this->assertSame('string', $column->getType()->type());
        $this->assertSame(['type' => 'string', 'param' => 'value'], $column->getType()->parameters());
    }
    
    public function testStorableMethod()
    {
        $this->assertTrue(Column\Text::new(name: 'name')->storable()->isStorable());
        $this->assertTrue(Column\Text::new(name: 'name')->storable(true)->isStorable());
        $this->assertFalse(Column\Text::new(name: 'name')->storable(false)->isStorable());
    }
    
    public function testReadMethod()
    {
        $reader = fn (string $value, array $attributes): string => ucfirst($value);
        
        $column = Column\Text::new(name: 'name')->read($reader);
        
        $this->assertSame('Lorem', $column->reading(value: 'lorem', attributes: []));
        $this->assertSame('1.5', $column->reading(value: 1.5, attributes: []));
        $this->assertSame('', $column->reading(value: [], attributes: []));
    }
    
    public function testWriteMethod()
    {
        $writer = fn (string $value, array $attributes): string => ucfirst($value);
        
        $column = Column\Text::new(name: 'name')->write($writer);
        
        $this->assertSame('Lorem', $column->writing(value: 'lorem', attributes: []));
        $this->assertSame('1.5', $column->writing(value: 1.5, attributes: []));
        $this->assertSame('', $column->writing(value: [], attributes: []));
    }
}