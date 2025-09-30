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
use Tobento\Service\Repository\Storage\Attribute\StringTranslations;
use Tobento\Service\Repository\Storage\Attribute\ArrayTranslations;
use InvalidArgumentException;

/**
 * TranslatableTest
 */
class TranslatableTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $column = new Column\Translatable(name: 'name');
        
        $this->assertInstanceof(Column\Translatable::class, $column);
        $this->assertInstanceof(Column\ColumnInterface::class, $column);
        $this->assertSame('name', $column->name());
        $this->assertSame('json', $column->getType()->type());
        $this->assertTrue($column->isStorable());
        $this->assertTrue($column->isTranslatable());
        $this->assertInstanceof(StringTranslations::class, $column->reading(value: ['en' => 'En'], attributes: []));
        $this->assertSame(['en' => 'En'], $column->writing(value: ['en' => 'En'], attributes: []));
    }
    
    public function testValidSubtypes()
    {
        $this->assertSame('json', new Column\Translatable(name: 'name', subtype: 'string')->getType()->type());
        $this->assertSame('json', new Column\Translatable(name: 'name', subtype: 'array')->getType()->type());
    }
    
    public function testThrowsInvalidArgumentExceptionIfInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        
        new Column\Translatable(name: 'name', subtype: 'foo');
    }
    
    public function testGetSubtypeMethod()
    {
        $this->assertSame('string', (new Column\Translatable(name: 'name'))->getSubtype());
        $this->assertSame('array', (new Column\Translatable(name: 'name', subtype: 'array'))->getSubtype());
    }
    
    public function testReadingMethod()
    {
        $column = new Column\Translatable(name: 'name');
        
        $this->assertInstanceof(StringTranslations::class, $column->reading(value: ['en' => 'En'], attributes: []));
        $this->assertInstanceof(StringTranslations::class, $column->reading(value: true, attributes: []));
        $this->assertInstanceof(StringTranslations::class, $column->reading(value: '', attributes: []));
        $this->assertInstanceof(StringTranslations::class, $column->reading(value: 1.5, attributes: []));
        $this->assertSame(['en' => 'En'], $column->reading(value: ['en' => 'En'], attributes: [])->all());
    }
    
    public function testReadingMethodWithArraySubtype()
    {
        $column = new Column\Translatable(name: 'name', subtype: 'array');
        
        $this->assertInstanceof(ArrayTranslations::class, $column->reading(value: ['en' => ['color' => 'red']], attributes: []));
        $this->assertInstanceof(ArrayTranslations::class, $column->reading(value: true, attributes: []));
        $this->assertInstanceof(ArrayTranslations::class, $column->reading(value: '', attributes: []));
        $this->assertInstanceof(ArrayTranslations::class, $column->reading(value: 1.5, attributes: []));
        $this->assertSame(['en' => ['color' => 'red']], $column->reading(value: ['en' => ['color' => 'red']], attributes: [])->all());
    }
    
    public function testWritingMethod()
    {
        $column = new Column\Translatable(name: 'name');
        
        $this->assertSame(['en' => 'En'], $column->writing(value: ['en' => 'En'], attributes: []));
        $this->assertSame([], $column->writing(value: true, attributes: []));
        $this->assertSame(['en' => ''], $column->writing(value: '', attributes: []));
        $this->assertSame([], $column->writing(value: [], attributes: []));
    }
    
    public function testWritingMethodWithArraySubtype()
    {
        $column = new Column\Translatable(name: 'name', subtype: 'array');
        
        $this->assertSame(['en' => ['color' => 'red']], $column->writing(value: ['en' => ['color' => 'red']], attributes: []));
        $this->assertSame([], $column->writing(value: true, attributes: []));
        $this->assertSame(['en' => ''], $column->writing(value: '', attributes: []));
        $this->assertSame([], $column->writing(value: [], attributes: []));
    }

    public function testTypeMethod()
    {
        $column = new Column\Translatable(name: 'name')->type(type: 'string', param: 'value');
        
        $this->assertSame('json', $column->getType()->type());
        $this->assertSame(['type' => 'json', 'param' => 'value'], $column->getType()->parameters());
    }
    
    public function testStorableMethod()
    {
        $this->assertTrue(new Column\Translatable(name: 'name')->storable()->isStorable());
        $this->assertTrue(new Column\Translatable(name: 'name')->storable(true)->isStorable());
        $this->assertFalse(new Column\Translatable(name: 'name')->storable(false)->isStorable());
    }
    
    public function testReadMethod()
    {
        $reader = fn (string $value, array $attributes, string $locale): string => strtoupper($value);
        
        $column = new Column\Translatable(name: 'name')->read($reader);
        
        $this->assertSame(['en' => 'EN'], $column->reading(value: ['en' => 'En'], attributes: [])->all());
        $this->assertSame([], $column->reading(value: 4.5, attributes: [])->all());
    }
    
    public function testReadMethodUsesOnlySpecifiedLocales()
    {
        $reader = fn (string $value, array $attributes, string $locale): string => strtoupper($value);
        
        $column = new Column\Translatable(name: 'name')->locales('de')->read($reader);
        
        $this->assertSame(['de' => 'DE'], $column->reading(value: ['en' => 'En', 'de' => 'De'], attributes: [])->all());
    }
    
    public function testReadMethodWithArraySubtype()
    {
        $reader = fn (array $value, array $attributes, string $locale): array => ['color' => 'blue'];
        
        $column = new Column\Translatable(name: 'name', subtype: 'array')->read($reader);
        
        $this->assertSame(['en' => ['color' => 'blue']], $column->reading(value: ['en' => ['color' => 'red']], attributes: [])->all());
        $this->assertSame([], $column->reading(value: 4.5, attributes: [])->all());
    }
    
    public function testReadingMethodWithArraySubtypeUsesOnlySpecifiedLocales()
    {
        $column = new Column\Translatable(name: 'name', subtype: 'array')->locales('de');
        
        $this->assertSame(
            ['de' => ['color' => 'rot']],
            $column->reading(value: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']], attributes: [])->all()
        );
    }
    
    public function testWriteMethod()
    {
        $writer = fn (string $value, array $attributes, string $locale): string => strtoupper($value);
        
        $column = new Column\Translatable(name: 'name')->write($writer);
        
        $this->assertSame(['en' => 'EN'], $column->writing(value: ['en' => 'En'], attributes: []));
        $this->assertSame([], $column->writing(value: 4.5, attributes: []));
    }
    
    public function testWriteMethodUsesOnlySpecifiedLocales()
    {
        $writer = fn (string $value, array $attributes, string $locale): string => strtoupper($value);
        
        $column = new Column\Translatable(name: 'name')->locales('de')->write($writer);
        
        $this->assertSame(['de' => 'DE'], $column->writing(value: ['en' => 'En', 'de' => 'De'], attributes: []));
    }
    
    public function testWriteMethodWithArraySubtype()
    {
        $writer = fn (array $value, array $attributes, string $locale): array => ['color' => 'blue'];
        
        $column = new Column\Translatable(name: 'name')->write($writer);
        
        $this->assertSame(['en' => ['color' => 'blue']], $column->writing(value: ['en' => ['color' => 'red']], attributes: []));
        $this->assertSame([], $column->writing(value: 4.5, attributes: []));
    }
    
    public function testWritingMethodWithArraySubtypeUsesOnlySpecifiedLocales()
    {
        $column = new Column\Translatable(name: 'name')->locales('de');
        
        $this->assertSame(
            ['de' => ['color' => 'rot']],
            $column->writing(value: ['en' => ['color' => 'red'], 'de' => ['color' => 'rot']], attributes: [])
        );
    }
}