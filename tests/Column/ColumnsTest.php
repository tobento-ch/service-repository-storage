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
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;
use Tobento\Service\Repository\Storage\Column\Columns;
use Tobento\Service\Repository\Storage\Column;
use IteratorAggregate;

/**
 * ColumnsTest
 */
class ColumnsTest extends TestCase
{
    public function testImplementsInterfaces()
    {
        $columns = new Columns();
        
        $this->assertInstanceof(ColumnsInterface::class, $columns);
        $this->assertInstanceof(IteratorAggregate::class, $columns);
    }
    
    public function testFilterMethod()
    {
        $columns = new Columns(
            Column\Text::new('foo'),
            Column\Text::new('bar')->storable(false),
        );
        
        $columnsNew = $columns->filter(
            fn(ColumnInterface $c): bool => $c->isStorable()
        );
        
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(1, count($columnsNew->all()));
    }
    
    public function testStorableMethod()
    {
        $columns = new Columns(
            Column\Text::new('foo'),
            Column\Text::new('bar')->storable(false),
            Column\Text::new('lorem')->storable(false),
        );
        
        $columnsNew = $columns->storable(false);
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(2, count($columnsNew->all()));
        
        $this->assertSame(1, count($columns->storable()->all()));
        $this->assertSame(1, count($columns->storable(true)->all()));
    }
    
    public function testTranslatableMethod()
    {
        $columns = new Columns(
            Column\Translatable::new('foo'),
            Column\Translatable::new('bar'),
            Column\Text::new('lorem'),
        );
        
        $columnsNew = $columns->translatable(false);
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(1, count($columnsNew->all()));
        
        $this->assertSame(2, count($columns->translatable()->all()));
        $this->assertSame(2, count($columns->translatable(true)->all()));
    }

    public function testOnlyMethod()
    {
        $columns = new Columns(
            Column\Translatable::new('foo'),
            Column\Translatable::new('bar'),
            Column\Text::new('lorem'),
        );
        
        $columnsNew = $columns->only(['foo', 'lorem']);
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(2, count($columnsNew->all()));
        $this->assertSame(1, count($columns->only(['bar'])->all()));
    }
    
    public function testExceptMethod()
    {
        $columns = new Columns(
            Column\Translatable::new('foo'),
            Column\Translatable::new('bar'),
            Column\Text::new('lorem'),
        );
        
        $columnsNew = $columns->except(['foo', 'lorem']);
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(1, count($columnsNew->all()));
        $this->assertSame(2, count($columns->except(['bar'])->all()));
    }
    
    public function testColumnMethod()
    {
        $columns = new Columns(
            Column\Text::new('foo'),
            Column\Text::new('bar'),
        );
        
        $this->assertSame(['foo', 'bar'], $columns->column(name: 'name'));
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $columns->column(name: 'name', index: 'name'));
    }
    
    public function testGetMethod()
    {
        $foo = Column\Text::new('foo');
        $bar = Column\Text::new('bar');
        $columns = new Columns($foo, $bar);
        
        $this->assertSame($foo, $columns->get(name: 'foo'));
        $this->assertSame(null, $columns->get(name: 'lorem'));
    }
    
    public function testAllMethod()
    {
        $foo = Column\Text::new('foo');
        $bar = Column\Text::new('bar');
        $columns = new Columns($foo, $bar);
        
        $this->assertSame(['foo' => $foo, 'bar' => $bar], $columns->all());
    }
    
    public function testEmptyMethod()
    {
        $this->assertTrue((new Columns())->empty());
        $this->assertFalse((new Columns(Column\Text::new('foo')))->empty());
    }
    
    public function testPrimaryMethod()
    {
        $this->assertSame(null, (new Columns())->primary());
        $this->assertSame(null, (new Columns(Column\Text::new('foo')))->primary());
        
        $id = Column\Id::new();
        $this->assertSame($id, (new Columns($id))->primary());
    }
    
    public function testProcessReadingMethod()
    {
        $columns = new Columns(
            Column\Text::new('foo'),
        );
                
        $this->assertSame(
            ['foo' => 'a'],
            $columns->processReading(attributes: ['foo' => 'a'])
        );
        
        $this->assertSame(
            ['foo' => 'a', 'bar' => 'a'],
            $columns->processReading(attributes: ['foo' => 'a', 'bar' => 'a'])
        );
    }
    
    public function testProcessWritingMethod()
    {
        $columns = new Columns(
            Column\Text::new('foo'),
        );
                
        $this->assertSame(
            ['foo' => 'a'],
            $columns->processWriting(attributes: ['foo' => 'a'])
        );
        
        $this->assertSame(
            ['foo' => 'a', 'bar' => 'a'],
            $columns->processWriting(attributes: ['foo' => 'a', 'bar' => 'a'])
        );
    }
    
    public function testProcessWritingMethodDefaultColumnValueGetsAdded()
    {
        $columns = new Columns(
            Column\Text::new('foo'),
            Column\Text::new('bar')->type(default: 'value'),
        );
                
        $this->assertSame(
            ['foo' => 'a', 'bar' => 'value'],
            $columns->processWriting(attributes: ['foo' => 'a'])
        );
        
        $this->assertSame(
            ['bar' => 'a'],
            $columns->processWriting(attributes: ['bar' => 'a'])
        );
    }
    
    public function testGetIteratorMethod()
    {
        $foo = Column\Text::new('foo');
        $bar = Column\Text::new('bar');
        $columns = new Columns($foo, $bar);
        
        $iterated = [];
        
        foreach($columns as $key => $value) {
            $iterated[$key] = $value;
        }
        
        $this->assertSame(['foo' => $foo, 'bar' => $bar], $iterated);
    }
}