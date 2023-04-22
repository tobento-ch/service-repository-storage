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
 * JsonTest
 */
class JsonTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $column = Column\Json::new(name: 'name');
        
        $this->assertInstanceof(Column\Json::class, $column);
        $this->assertInstanceof(Column\ColumnInterface::class, $column);
        $this->assertSame('name', $column->name());
        $this->assertSame('json', $column->getType()->type());
        $this->assertTrue($column->isStorable());
        $this->assertFalse($column->isTranslatable());
        $this->assertSame(['key' => 'value'], $column->reading(value: ['key' => 'value'], attributes: []));
        $this->assertSame(['key' => 'value'], $column->writing(value: ['key' => 'value'], attributes: []));
    }

    public function testReadingMethod()
    {
        $column = Column\Json::new(name: 'name');
        
        $this->assertSame(['key' => 'value'], $column->reading(value: ['key' => 'value'], attributes: []));
        $this->assertIsArray($column->reading(value: true, attributes: []));
        $this->assertIsArray($column->reading(value: '', attributes: []));
        $this->assertIsArray($column->reading(value: 1.5, attributes: []));
    }
    
    public function testWritingMethod()
    {
        $column = Column\Json::new(name: 'name');
        
        $this->assertSame(['key' => 'value'], $column->writing(value: ['key' => 'value'], attributes: []));
        $this->assertIsArray($column->writing(value: true, attributes: []));
        $this->assertIsArray($column->writing(value: '', attributes: []));
        $this->assertIsArray($column->writing(value:1.5, attributes: []));
    }

    public function testTypeMethod()
    {
        $column = Column\Json::new(name: 'name')->type(type: 'int', param: 'value');
        
        $this->assertSame('json', $column->getType()->type());
        $this->assertSame(['type' => 'json', 'param' => 'value'], $column->getType()->parameters());
    }
    
    public function testStorableMethod()
    {
        $this->assertTrue(Column\Json::new(name: 'name')->storable()->isStorable());
        $this->assertTrue(Column\Json::new(name: 'name')->storable(true)->isStorable());
        $this->assertFalse(Column\Json::new(name: 'name')->storable(false)->isStorable());
    }
    
    public function testReadMethod()
    {
        $reader = fn (array $value, array $attributes): array => ['key' => 'new'];
        
        $column = Column\Json::new(name: 'name')->read($reader);
        
        $this->assertSame(['key' => 'new'], $column->reading(value: ['key' => 'value'], attributes: []));
        $this->assertSame(['key' => 'new'], $column->reading(value: 'foo', attributes: []));
    }
    
    public function testWriteMethod()
    {
        $writer = fn (array $value, array $attributes): array => ['key' => 'new'];
        
        $column = Column\Json::new(name: 'name')->write($writer);
        
        $this->assertSame(['key' => 'new'], $column->writing(value: ['key' => 'value'], attributes: []));
        $this->assertSame(['key' => 'new'], $column->writing(value: 'foo', attributes: []));
    }
}