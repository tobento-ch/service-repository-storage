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
use Tobento\Service\Repository\Storage\Test\Helper\Dates;
use Tobento\Service\Dater\DateFormatter;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * DatetimeTest
 */
class DatetimeTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $column = Column\Datetime::new(name: 'name');
        
        $this->assertInstanceof(Column\Datetime::class, $column);
        $this->assertInstanceof(Column\ColumnInterface::class, $column);
        $this->assertSame('name', $column->name());
        $this->assertSame('datetime', $column->getType()->type());
        $this->assertTrue($column->isStorable());
        $this->assertFalse($column->isTranslatable());
        $this->assertSame('2023-11-25 00:00:00', $column->reading(value: '2023-11-25 00:00:00', attributes: []));
        $this->assertSame('2023-11-25 00:00:00', $column->writing(value: '2023-11-25 00:00:00', attributes: []));
    }
    
    public function testValidTypes()
    {
        $this->assertSame('datetime', Column\Datetime::new(name: 'name', type: 'datetime')->getType()->type());
        $this->assertSame('date', Column\Datetime::new(name: 'name', type: 'date')->getType()->type());
        $this->assertSame('time', Column\Datetime::new(name: 'name', type: 'time')->getType()->type());
        $this->assertSame('timestamp', Column\Datetime::new(name: 'name', type: 'timestamp')->getType()->type());
    }
    
    public function testThrowsInvalidArgumentExceptionIfInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        
        Column\Datetime::new(name: 'name', type: 'foo');
    }    
    
    public function testReadingMethod()
    {
        $column = Column\Datetime::new(name: 'name');
        
        $this->assertIsString($column->reading(value: true, attributes: []));
        $this->assertIsString($column->reading(value: 1, attributes: []));
        $this->assertIsString($column->reading(value: '', attributes: []));
        $this->assertIsString($column->reading(value: [], attributes: []));
    }
    
    public function testWritingMethodWithDatetime()
    {
        $column = Column\Datetime::new(name: 'name', type: 'datetime');
        
        $this->assertSame('2023-11-25 10:09:08', $column->writing(value: '2023-11-25 10:09:08', attributes: []));
        $this->assertTrue(Dates::isDateFormat('Y-m-d H:i:s', $column->writing(value: true, attributes: [])));
        $this->assertTrue(Dates::isDateFormat('Y-m-d H:i:s', $column->writing(value: '', attributes: [])));
        $this->assertTrue(Dates::isDateFormat('Y-m-d H:i:s', $column->writing(value: 2, attributes: [])));
        $this->assertTrue(Dates::isDateFormat('Y-m-d H:i:s', $column->writing(value: [], attributes: [])));
    }
    
    public function testWritingMethodWithDate()
    {
        $column = Column\Datetime::new(name: 'name', type: 'date');
        
        $this->assertSame('2023-11-25', $column->writing(value: '2023-11-25', attributes: []));
        $this->assertSame('2023-11-25', $column->writing(value: '2023-11-25 10:09:08', attributes: []));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $column->writing(value: true, attributes: [])));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $column->writing(value: '', attributes: [])));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $column->writing(value: 2, attributes: [])));
        $this->assertTrue(Dates::isDateFormat('Y-m-d', $column->writing(value: [], attributes: [])));
    }
    
    public function testWritingMethodWithTime()
    {
        $column = Column\Datetime::new(name: 'name', type: 'time');
        
        $this->assertSame('10:09:08', $column->writing(value: '10:09:08', attributes: []));
        $this->assertSame('10:09:08', $column->writing(value: '2023-11-25 10:09:08', attributes: []));
        $this->assertTrue(Dates::isDateFormat('H:i:s', $column->writing(value: true, attributes: [])));
        $this->assertTrue(Dates::isDateFormat('H:i:s', $column->writing(value: '', attributes: [])));
        $this->assertTrue(Dates::isDateFormat('H:i:s', $column->writing(value: 2, attributes: [])));
        $this->assertTrue(Dates::isDateFormat('H:i:s', $column->writing(value: [], attributes: [])));
    }
    
    public function testWritingMethodWithTimestamp()
    {
        $column = Column\Datetime::new(name: 'name', type: 'timestamp');
        
        $this->assertTrue(Dates::isTimestamp($column->writing(value: true, attributes: [])));
        $this->assertTrue(Dates::isTimestamp($column->writing(value: '', attributes: [])));
        $this->assertTrue(Dates::isTimestamp($column->writing(value: 2, attributes: [])));
        $this->assertTrue(Dates::isTimestamp($column->writing(value: [], attributes: [])));
    }
    
    public function testTypeMethod()
    {
        $column = Column\Datetime::new(name: 'name')->type(type: 'array', param: 'value');
        
        $this->assertSame('datetime', $column->getType()->type());
        $this->assertSame(['type' => 'datetime', 'param' => 'value'], $column->getType()->parameters());
    }
    
    public function testStorableMethod()
    {
        $this->assertTrue(Column\Datetime::new(name: 'name')->storable()->isStorable());
        $this->assertTrue(Column\Datetime::new(name: 'name')->storable(true)->isStorable());
        $this->assertFalse(Column\Datetime::new(name: 'name')->storable(false)->isStorable());
    }
    
    public function testReadMethod()
    {
        $reader = fn (mixed $value, array $attributes, DateFormatter $df)
            : DateTimeImmutable => $df->toDateTime(value: $value);
        
        $column = Column\Datetime::new(name: 'name')->read($reader);
        
        $this->assertInstanceof(DateTimeImmutable::class, $column->reading(value: '2023-11-25 10:09:08', attributes: []));
    }
    
    public function testWriteMethod()
    {
        $writer = fn (mixed $value, array $attributes, DateFormatter $df)
            : string => $df->format(value: $value, format: 'Y');
        
        $column = Column\Datetime::new(name: 'name')->write($writer);
        
        $this->assertSame('2023', $column->writing(value: '2023-11-25 10:09:08', attributes: []));
    }
}