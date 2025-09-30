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

namespace Tobento\Service\Repository\Storage\Test\Database;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Repository\Storage\Database\SchemaTableFactory;
use Tobento\Service\Repository\Storage\Column\Columns;
use Tobento\Service\Repository\Storage\Column;
use Tobento\Service\Database\Schema\Table;
use Tobento\Service\Database\Schema\Index;

/**
 * SchemaTableFactoryTest
 */
class SchemaTableFactoryTest extends TestCase
{
    public function testCreateTableFromColumnsMethod()
    {
        $factory = new SchemaTableFactory();
        
        $table = $factory->createTableFromColumns(
            tableName: 'products',
            columns: new Columns(
                new Column\Id('id'),
                new Column\Text('foo'),
            ),
        );
        
        $this->assertInstanceof(Table::class, $table);
    }
    
    public function testCreateTableFromColumnsMethodUsesOnlyStorableColumns()
    {
        $factory = new SchemaTableFactory();
        
        $table = $factory->createTableFromColumns(
            tableName: 'products',
            columns: new Columns(
                new Column\Id('id'),
                new Column\Text('foo'),
                new Column\Text('bar')->storable(false),
            ),
        );
        
        $this->assertSame(['id', 'foo'], array_keys($table->getColumns()));
    }
    
    public function testCreateTableFromColumnsMethodUsesTypeParameters()
    {
        $factory = new SchemaTableFactory();
        
        $table = $factory->createTableFromColumns(
            tableName: 'products',
            columns: new Columns(
                new Column\Text('foo')
                    ->type(length: 150, nullable: false, default: 'foo'),
                new Column\Integer('bar')
                    ->type(
                        unsigned: true,
                        index: ['name' => 'index_name', 'column' => 'name', 'unique' => true, 'primary' => true],
                    ),
            ),
        );
        
        $column = $table->getColumn('foo');
        
        $this->assertSame(150, $column->getLength());
        $this->assertSame(false, $column->isNullable());
        $this->assertSame('foo', $column->getDefault());
        
        $column = $table->getColumn('bar');
        $this->assertSame(true, $column->isNullable());
        $this->assertSame(true, $column->isUnsigned());
        $this->assertSame(null, $column->getDefault());

        $this->assertInstanceof(Index::class, $table->getIndexes()['index_name'] ?? null);
    }
    
    public function testCreateTableFromColumnsMethodAddsIndexColumnIfNotExists()
    {
        $factory = new SchemaTableFactory();
        
        $table = $factory->createTableFromColumns(
            tableName: 'products',
            columns: new Columns(
                new Column\Id('id'),
                new Column\Integer('foo')
                    ->type(
                        unsigned: true,
                        index: ['name' => 'index_name', 'unique' => true, 'primary' => true],
                    ),
            ),
        );
        
        $index = $table->getIndexes()['index_name'] ?? null;
        $this->assertSame(['foo'], $index?->getColumns());
    }
}