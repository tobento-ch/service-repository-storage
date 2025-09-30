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

/**
 * BooleanTest
 */
class BooleanTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $column = new Column\Boolean(name: 'name');
        
        $this->assertInstanceof(Column\Boolean::class, $column);
        $this->assertInstanceof(Column\ColumnInterface::class, $column);
        $this->assertSame('name', $column->name());
        $this->assertSame('bool', $column->getType()->type());
        $this->assertTrue($column->isStorable());
        $this->assertFalse($column->isTranslatable());
        $this->assertTrue($column->reading(value: true, attributes: []));
        $this->assertTrue($column->writing(value: true, attributes: []));
    }
    
    public function testReadingMethod()
    {
        $column = new Column\Boolean(name: 'name');
        
        $this->assertTrue($column->reading(value: true, attributes: []));
        $this->assertTrue($column->reading(value: 'f', attributes: []));
        $this->assertTrue($column->reading(value: 1, attributes: []));
        $this->assertFalse($column->reading(value: false, attributes: []));
        $this->assertFalse($column->reading(value: '', attributes: []));
        $this->assertFalse($column->reading(value: 0, attributes: []));
        $this->assertFalse($column->reading(value: [], attributes: []));
    }
    
    public function testWritingMethod()
    {
        $column = new Column\Boolean(name: 'name');
        
        $this->assertTrue($column->writing(value: true, attributes: []));
        $this->assertTrue($column->writing(value: 'f', attributes: []));
        $this->assertTrue($column->writing(value: 1, attributes: []));
        $this->assertFalse($column->writing(value: false, attributes: []));
        $this->assertFalse($column->writing(value: '', attributes: []));
        $this->assertFalse($column->writing(value: 0, attributes: []));
        $this->assertFalse($column->writing(value: [], attributes: []));
    }
    
    public function testTypeMethod()
    {
        $column = new Column\Boolean(name: 'name')->type(type: 'array', param: 'value');
        
        $this->assertSame('bool', $column->getType()->type());
        $this->assertSame(['type' => 'bool', 'param' => 'value'], $column->getType()->parameters());
    }
    
    public function testStorableMethod()
    {
        $this->assertTrue(new Column\Boolean(name: 'name')->storable()->isStorable());
        $this->assertTrue(new Column\Boolean(name: 'name')->storable(true)->isStorable());
        $this->assertFalse(new Column\Boolean(name: 'name')->storable(false)->isStorable());
    }
    
    public function testReadMethod()
    {
        $column = new Column\Boolean(name: 'name')
            ->read(fn (bool $value, array $attributes): bool => !$value);
        
        $this->assertFalse($column->reading(value: true, attributes: []));
        $this->assertTrue($column->reading(value: [], attributes: []));
    }
    
    public function testWriteMethod()
    {
        $column = new Column\Boolean(name: 'name')
            ->write(fn (bool $value, array $attributes): bool => !$value);
        
        $this->assertFalse($column->writing(value: true, attributes: []));
        $this->assertTrue($column->writing(value: [], attributes: []));
    }
}