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

use IteratorAggregate;
use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Column\AliasedColumns;
use Tobento\Service\Repository\Storage\Column\ColumnsInterface;
use Tobento\Service\Repository\Storage\Column\ColumnInterface;
use Tobento\Service\Repository\Storage\Column\Columns;
use Tobento\Service\Repository\Storage\Column;

class AliasedColumnsTest extends TestCase
{
    public function testImplementsInterfaces()
    {
        $columns = new AliasedColumns();
        
        $this->assertInstanceof(ColumnsInterface::class, $columns);
        $this->assertInstanceof(IteratorAggregate::class, $columns);
    }
    
    public function testFilterMethod()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
            new Column\Text('bar')->storable(false),
        );
        
        $columnsNew = $columns->filter(
            fn(ColumnInterface $c): bool => $c->isStorable()
        );
        
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(1, count($columnsNew->all()));
    }
    
    public function testStorableMethod()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
            new Column\Text('bar')->storable(false),
            new Column\Text('lorem')->storable(false),
        );
        
        $columnsNew = $columns->storable(false);
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(2, count($columnsNew->all()));
        
        $this->assertSame(1, count($columns->storable()->all()));
        $this->assertSame(1, count($columns->storable(true)->all()));
    }
    
    public function testTranslatableMethod()
    {
        $columns = new AliasedColumns(
            new Column\Translatable('foo'),
            new Column\Translatable('bar'),
            new Column\Text('lorem'),
        );
        
        $columnsNew = $columns->translatable(false);
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(1, count($columnsNew->all()));
        
        $this->assertSame(2, count($columns->translatable()->all()));
        $this->assertSame(2, count($columns->translatable(true)->all()));
    }

    public function testOnlyMethod()
    {
        $columns = new AliasedColumns(
            new Column\Translatable('foo'),
            new Column\Translatable('bar'),
            new Column\Text('lorem'),
        );
        
        $columnsNew = $columns->only(['foo', 'lorem']);
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(2, count($columnsNew->all()));
        $this->assertSame(1, count($columns->only(['bar'])->all()));
    }
    
    public function testExceptMethod()
    {
        $columns = new AliasedColumns(
            new Column\Translatable('foo'),
            new Column\Translatable('bar'),
            new Column\Text('lorem'),
        );
        
        $columnsNew = $columns->except(['foo', 'lorem']);
        $this->assertFalse($columns === $columnsNew);
        $this->assertSame(1, count($columnsNew->all()));
        $this->assertSame(2, count($columns->except(['bar'])->all()));
    }
    
    public function testColumnMethod()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
            new Column\Text('bar'),
        );
        
        $this->assertSame(['foo', 'bar'], $columns->column(name: 'name'));
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $columns->column(name: 'name', index: 'name'));
    }
    
    public function testGetMethod()
    {
        $foo = new Column\Text('foo');
        $bar = new Column\Text('bar');
        $columns = new AliasedColumns($foo, $bar);
        
        $this->assertSame($foo, $columns->get(name: 'foo'));
        $this->assertSame(null, $columns->get(name: 'lorem'));
    }
    
    public function testGetMethodResolvesAliasToRaw()
    {
        $columns = (new AliasedColumns(
            new Column\Text('foo'),
            new Column\Text('bar'),
        ))->withAliases(['aliasFoo' => 'foo']);

        $this->assertSame($columns->get('foo'), $columns->get('aliasFoo'));
    }
    
    public function testAllMethod()
    {
        $foo = new Column\Text('foo');
        $bar = new Column\Text('bar');
        $columns = new AliasedColumns($foo, $bar);
        
        $this->assertSame(['foo' => $foo, 'bar' => $bar], $columns->all());
    }
    
    public function testEmptyMethod()
    {
        $this->assertTrue((new AliasedColumns())->empty());
        $this->assertFalse((new AliasedColumns(new Column\Text('foo')))->empty());
    }
    
    public function testPrimaryMethod()
    {
        $this->assertSame(null, (new AliasedColumns())->primary());
        $this->assertSame(null, (new AliasedColumns(new Column\Text('foo')))->primary());
        
        $id = new Column\Id();
        $this->assertSame($id, (new AliasedColumns($id))->primary());
    }
    
    public function testProcessReadingMethod()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
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
    
    public function testProcessReadingMethodAddsAliasValues()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
        )->withAliases(['aliasFoo' => 'foo']);

        $result = $columns->processReading(['foo' => 'value']);

        $this->assertSame('value', $result['aliasFoo']);
    }
    
    public function testProcessReadingMethodAddsJsonPathAliasValues()
    {
        $columns = new AliasedColumns(
            new Column\Json('options'),
        )->withAliases(['aliasColor' => 'options->color']);

        $result = $columns->processReading([
            'options' => ['color' => 'blue']
        ]);

        $this->assertSame('blue', $result['aliasColor']);
    }

    public function testProcessWritingMethod()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
        );
                
        $this->assertSame(
            ['foo' => 'a'],
            $columns->processWriting(attributes: ['foo' => 'a'], action: 'name')
        );
        
        $this->assertSame(
            ['foo' => 'a', 'bar' => 'a'],
            $columns->processWriting(attributes: ['foo' => 'a', 'bar' => 'a'], action: 'name')
        );
    }

    public function testProcessWritingMethodWithForcedColumn()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
            new Column\Text('bar')->forceWriting(),
            new Column\Text('baz')->write(fn () => 'value')->forceWriting(),
            new Column\Text('lorem')->type(default: 'ipsum')->forceWriting(),
        );
        
        $this->assertSame(
            ['bar' => '', 'baz' => 'value', 'lorem' => 'ipsum'],
            $columns->processWriting(attributes: [], action: 'create')
        );
        
        $this->assertSame(
            ['bar' => '', 'baz' => 'value', 'lorem' => 'ipsum'],
            $columns->processWriting(attributes: [], action: 'update')
        );
        
        $this->assertSame(
            ['bar' => 'a', 'baz' => 'value', 'lorem' => 'c'],
            $columns->processWriting(attributes: ['bar' => 'a', 'baz' => 'b', 'lorem' => 'c'], action: 'name')
        );
    }
    
    public function testProcessWritingMethodWithForcedColumnSingleAction()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
            new Column\Text('bar')->type(nullable: true)->forceWriting(force: true, action: 'create'),
            new Column\Text('baz')->write(fn () => 'value')->forceWriting(force: true, action: 'create'),
            new Column\Text('lorem')->type(default: 'ipsum')->forceWriting(force: true, action: 'create'),
        );
        
        $this->assertSame(
            ['bar' => null, 'baz' => 'value', 'lorem' => 'ipsum'],
            $columns->processWriting(attributes: [], action: 'create')
        );
        
        $this->assertSame(
            [],
            $columns->processWriting(attributes: [], action: 'update')
        );
    }
    
    public function testProcessWritingMethodDefaultColumnValueGetsAddedOnCreateAction()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
            new Column\Text('bar')->type(default: 'value'),
        );
        
        $this->assertSame(
            ['foo' => 'a', 'bar' => 'value'],
            $columns->processWriting(attributes: ['foo' => 'a'], action: 'create')
        );
        
        $this->assertSame(
            ['bar' => 'a'],
            $columns->processWriting(attributes: ['bar' => 'a'], action: 'create')
        );
    }
    
    public function testProcessWritingMethodDefaultColumnValueGetsNotAddedOnUpdateAction()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
            new Column\Text('bar')->type(default: 'value'),
        );
        
        $this->assertSame(
            ['foo' => 'a'],
            $columns->processWriting(attributes: ['foo' => 'a'], action: 'update')
        );
        
        $this->assertSame(
            [],
            $columns->processWriting(attributes: [], action: 'update')
        );
    }    
    
    public function testProcessWritingMethodRewritesAliasToRaw()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
        )->withAliases(['aliasFoo' => 'foo'], readonly: false);

        $result = $columns->processWriting(['aliasFoo' => 'value'], 'create');

        $this->assertSame('value', $result['foo']);
        $this->assertArrayNotHasKey('aliasFoo', $result);
    }
    
    public function testProcessWritingMethodReadonlyAliasesIgnoreAliasWrites()
    {
        $columns = new AliasedColumns(
            new Column\Text('foo'),
        )->withAliases(['aliasFoo' => 'foo'], readonly: true);

        $result = $columns->processWriting(['aliasFoo' => 'value'], 'create');

        // alias is removed
        $this->assertArrayNotHasKey('aliasFoo', $result);

        // raw is NOT written
        $this->assertArrayNotHasKey('foo', $result);
    }
    
    public function testProcessWritingMethodRewritesJsonPathAliasToRaw()
    {
        $columns = new AliasedColumns(
            new Column\Json('options'),
        )->withAliases(['product_color' => 'options->color'], readonly: false);

        $result = $columns->processWriting(['product_color' => 'blue'], 'create');

        // JSON structure created
        $this->assertArrayHasKey('options', $result);
        $this->assertSame('blue', $result['options']['color']);

        // alias removed
        $this->assertArrayNotHasKey('product_color', $result);
    }

    public function testGetIteratorMethod()
    {
        $foo = new Column\Text('foo');
        $bar = new Column\Text('bar');
        $columns = new AliasedColumns($foo, $bar);
        
        $iterated = [];
        
        foreach($columns as $key => $value) {
            $iterated[$key] = $value;
        }
        
        $this->assertSame(['foo' => $foo, 'bar' => $bar], $iterated);
    }
    
    public function testWithAliasesKeepsValidAlias()
    {
        $columns = new AliasedColumns(
            new Column\Text('title'),
            new Column\Text('name'),
            new Column\Boolean('active'),
        );

        $new = $columns->withAliases(['display_name' => 'title']);

        $this->assertSame(['display_name' => 'title'], $new->aliases());
    }

    public function testWithAliasesKeepsJsonPathAlias()
    {
        $columns = new AliasedColumns(
            new Column\Json('options'),
            new Column\Text('title'),
        );

        $new = $columns->withAliases(['product_color' => 'options->color']);

        $this->assertSame(['product_color' => 'options->color'], $new->aliases());
    }

    public function testWithAliasesRemovesJsonPathAliasIfBaseColumnMissing()
    {
        $columns = new AliasedColumns(
            new Column\Text('title'),
        );

        $new = $columns->withAliases(['product_color' => 'options->color']);

        $this->assertSame([], $new->aliases());
    }

    public function testWithAliasesIgnoresAliasCollidingWithRawColumn()
    {
        $columns = new AliasedColumns(
            new Column\Text('title'),
            new Column\Text('name'),
        );

        // raw column 'name' exists → alias ignored
        $new = $columns->withAliases(['name' => 'title']);

        $this->assertSame([], $new->aliases());
    }

    public function testWithAliasesIgnoresAliasWithMissingRawTarget()
    {
        $columns = new AliasedColumns(
            new Column\Text('title'),
            new Column\Text('name'),
        );

        // raw column 'missing' does not exist → alias ignored
        $new = $columns->withAliases(['display_name' => 'missing']);

        $this->assertSame([], $new->aliases());
    }

    public function testWithAliasesFiltersMultipleAliases()
    {
        $columns = new AliasedColumns(
            new Column\Text('title'),
            new Column\Text('name'),
            new Column\Boolean('active'),
        );

        $new = $columns->withAliases([
            'name'          => 'title',   // ignored (collides with raw 'name')
            'display_name'  => 'title',   // ok
            'foo'           => 'missing', // ignored (raw missing)
            'is_active'     => 'active',  // ok
        ]);

        $this->assertSame(
            [
                'display_name' => 'title',
                'is_active'    => 'active',
            ],
            $new->aliases()
        );
    }

    public function testWithAliasesReadonlyFlagIsStored()
    {
        $columns = new AliasedColumns(
            new Column\Text('title'),
        );

        $new = $columns->withAliases(['display_name' => 'title'], readonly: false);

        $this->assertFalse($new->readonlyAliases());
    }

    public function testWithAliasesReadonlyFlagDefaultsToTrue()
    {
        $columns = new AliasedColumns(
            new Column\Text('title'),
        );

        $new = $columns->withAliases(['display_name' => 'title']);

        $this->assertTrue($new->readonlyAliases());
    }
}